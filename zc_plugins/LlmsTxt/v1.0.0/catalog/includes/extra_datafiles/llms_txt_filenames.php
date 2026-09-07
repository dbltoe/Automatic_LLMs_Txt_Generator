<?php
/**
 * LLMs.txt Generator -- storefront page name.
 *
 * `extra_datafiles` is read by every supported release (v1.5.8 -> v3.0.0). A
 * root-level `filenames.php` is only picked up from v2.2.0, so it is not used.
 * Guarded because v2.2.0+ would otherwise define it twice if one were added.
 *
 * @package  LlmsTxt
 * @license  http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 */

if (!defined('FILENAME_LLMS_TXT')) {
    define('FILENAME_LLMS_TXT', 'llms_txt');
}
