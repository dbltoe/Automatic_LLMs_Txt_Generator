<?php
/**
 * LLMs.txt Generator -- storefront maintenance of a published file.
 *
 * Loaded at autoload breakpoint 178 on every storefront request. All it does
 * is ask the generator whether a published llms.txt is due for a rebuild,
 * which the generator answers from a timestamp in its state file on all but
 * one request in fifteen minutes. The llms_txt page looks after itself, so it
 * is skipped here.
 *
 * @package  LlmsTxt
 * @license  http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 */

if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
}

if (!isset($_GET['main_page']) || (string)$_GET['main_page'] !== 'llms_txt') {
    require_once dirname(__DIR__, 3) . '/shared/generator.php';
    llmstxt_maintain($db);
}
