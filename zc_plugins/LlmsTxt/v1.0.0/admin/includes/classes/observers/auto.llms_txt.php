<?php
/**
 * LLMs.txt Generator -- admin observer that flags catalog changes.
 *
 * Auto-loaded by includes/init_includes/init_observers.php, which every
 * supported release runs for the admin as well as the storefront, from this
 * plugin's admin/includes/classes/observers/ directory. The class name must
 * stay 'zcObserver' . camelize('llms_txt').
 *
 * It does not rebuild anything. A save in the admin sets one flag in the
 * plugin's state file, and the storefront's next check (or the next dynamic
 * request) does the build. That keeps every admin save as fast as it was.
 *
 * Every notifier here is present from v1.5.8 through v3.0.0-dev, checked
 * against the source of each release rather than the documentation. The
 * fingerprint comparison covers whatever an observer misses (a delete, a
 * status flip from the listing), so this is the fast path, not the only one.
 *
 * @package  LlmsTxt
 * @license  http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 */

if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
}

class zcObserverLlmsTxt extends base
{
    public function __construct()
    {
        $this->attach($this, [
            'NOTIFY_MODULES_UPDATE_PRODUCT_END',
            'NOTIFY_ADMIN_CATEGORIES_UPDATE_OR_INSERT_FINISH',
            'NOTIFY_ADMIN_EZPAGES_UPDATE_BASE',
            'NOTIFY_ADMIN_EZPAGES_UPDATE_LANG_INSERT',
            'NOTIFY_ADMIN_EZPAGES_UPDATE_LANG_UPDATE',
        ]);
    }

    /**
     * Generic handler: every event is treated the same way.
     *
     * Declared with the full five arguments Zen Cart passes, all optional, so
     * the same method satisfies the notify() call on every release.
     */
    public function update(&$class, $eventID, $param1 = [], &$param2 = null, &$param3 = null)
    {
        $this->llmstxtFlag((string)$eventID);
    }

    /**
     * @param string $eventID
     * @return void
     */
    protected function llmstxtFlag($eventID)
    {
        static $done = false;
        if ($done) {
            return;
        }
        $done = true;

        try {
            require_once dirname(__DIR__, 4) . '/shared/generator.php';
            llmstxt_mark_dirty(strtolower(str_replace(['NOTIFY_', 'ADMIN_', 'MODULES_'], '', $eventID)));
        } catch (Throwable $e) {
            // Never let a bookkeeping failure interrupt a product save.
        }
    }
}
