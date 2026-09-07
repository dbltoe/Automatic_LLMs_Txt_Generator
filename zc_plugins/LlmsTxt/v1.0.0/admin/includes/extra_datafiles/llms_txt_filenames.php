<?php
/**
 * LLMs.txt Generator -- admin page name.
 *
 * The same constant, and the same value, as the storefront side: the admin
 * page is `admin/llms_txt.php` (reached as `index.php?cmd=llms_txt`) and the
 * storefront page is `index.php?main_page=llms_txt`. Each side loads only its
 * own extra_datafiles, so there is no double definition -- but the guard stays,
 * because it costs nothing and a future core change could load both.
 *
 * @package  LlmsTxt
 * @license  http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 */

if (!defined('FILENAME_LLMS_TXT')) {
    define('FILENAME_LLMS_TXT', 'llms_txt');
}
