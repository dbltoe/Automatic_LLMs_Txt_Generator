<?php
/**
 * Automatic LLMs.txt Generator -- storefront page name.
 *
 * `extra_datafiles` is read by every supported release (v1.5.8 -> v3.0.0). A
 * root-level `filenames.php` is only picked up from v2.2.0, so it is not used.
 * Guarded because v2.2.0+ would otherwise define it twice if one were added.
 *
 * @package  LlmsTxt
 * @license  https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License v2.0
 */

if (!defined('FILENAME_LLMS_TXT')) {
    define('FILENAME_LLMS_TXT', 'llms_txt');
}
