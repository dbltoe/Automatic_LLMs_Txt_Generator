<?php
/**
 * LLMs.txt Generator -- storefront autoloader configuration.
 *
 * Breakpoint 178 runs after `init_observers.php` (175) and before
 * `init_header.php` (180). By then the database, the configuration constants,
 * the general function library and the language files are all loaded, and
 * nothing has produced output yet -- so the init script can keep a published
 * llms.txt file up to date without touching the page being served.
 *
 * @package  LlmsTxt
 * @license  http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 */

if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
}

$autoLoadConfig[178][] = [
    'autoType' => 'init_script',
    'loadFile' => 'init_llms_txt.php',
];
