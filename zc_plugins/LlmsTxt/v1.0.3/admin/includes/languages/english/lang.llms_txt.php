<?php
/**
 * Automatic LLMs.txt Generator -- admin page strings (English).
 *
 * @package  LlmsTxt
 * @license  http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 */

$define = [
    'HEADING_TITLE' => 'Automatic LLMs.txt Generator',

    'LLMSTXT_ADMIN_LEAD' => 'llms.txt is a short Markdown guide to your store that AI assistants read, the way search engines read sitemap.xml. This page shows what\'s published, rebuilds it on request, and previews the current output. The wording comes from your settings under Configuration.',

    'LLMSTXT_ADMIN_SECTION_STATUS' => 'Status',
    'LLMSTXT_ADMIN_SECTION_ACTIONS' => 'Actions',
    'LLMSTXT_ADMIN_SECTION_REWRITE' => 'Serving it at /llms.txt without a file',
    'LLMSTXT_ADMIN_SECTION_PREVIEW' => 'Preview',

    'LLMSTXT_ADMIN_ROW_ENABLED' => 'Plugin',
    'LLMSTXT_ADMIN_ROW_OUTPUT' => 'Published as',
    'LLMSTXT_ADMIN_ROW_REBUILD' => 'Rebuilds',
    'LLMSTXT_ADMIN_ROW_FILE' => 'llms.txt file',
    'LLMSTXT_ADMIN_ROW_FULL' => 'llms-full.txt file',
    'LLMSTXT_ADMIN_ROW_DYNAMIC' => 'Live page',
    'LLMSTXT_ADMIN_ROW_LAST_BUILT' => 'Last built',
    'LLMSTXT_ADMIN_ROW_CONTENTS' => 'Contents',
    'LLMSTXT_ADMIN_ROW_PENDING' => 'Pending',
    'LLMSTXT_ADMIN_ROW_CACHE' => 'Cache directory',

    'LLMSTXT_ADMIN_ENABLED' => 'Enabled',
    'LLMSTXT_ADMIN_DISABLED' => 'Disabled. Nothing\'s served and no file is written until it\'s switched on under Configuration.',
    'LLMSTXT_ADMIN_OUTPUT_FILE' => 'a file at the store root',
    'LLMSTXT_ADMIN_OUTPUT_DYNAMIC' => 'the live page only',
    'LLMSTXT_ADMIN_OUTPUT_BOTH' => 'a file at the store root, and the live page',
    'LLMSTXT_ADMIN_REBUILD_CHANGE' => 'when the catalog changes',
    'LLMSTXT_ADMIN_REBUILD_DAILY' => 'daily',
    'LLMSTXT_ADMIN_REBUILD_WEEKLY' => 'weekly',
    'LLMSTXT_ADMIN_REBUILD_MONTHLY' => 'monthly',
    'LLMSTXT_ADMIN_REBUILD_MANUAL' => 'only when you press Regenerate',
    'LLMSTXT_ADMIN_REBUILD_NOTE' => 'Automatic rebuilds happen on a storefront visit, at most once every fifteen minutes, so nobody in the admin waits for one.',

    'LLMSTXT_ADMIN_FILE_PRESENT' => 'Present, %s bytes, written %s.',
    'LLMSTXT_ADMIN_FILE_ABSENT' => 'Not present.',
    'LLMSTXT_ADMIN_FILE_ABSENT_EXPECTED' => 'Not present yet. Press Regenerate, or visit the storefront once.',
    'LLMSTXT_ADMIN_FILE_NOT_WANTED' => 'Not used with this output setting.',
    'LLMSTXT_ADMIN_FILE_FOREIGN' => 'A file this plugin didn\'t write is already there. It\'s been left alone. Use Replace if you\'d rather have the generated one.',
    'LLMSTXT_ADMIN_FILE_UNWRITABLE' => 'The store root isn\'t writable by the web server, so the file can\'t be written. Either make it writable, or switch the output setting to the live page and add the rewrite rule below.',
    'LLMSTXT_ADMIN_FILE_STALE' => 'A change is waiting; the file\'s rebuilt on the next storefront check, or now if you press Regenerate.',

    'LLMSTXT_ADMIN_DYNAMIC_ON' => 'Serving at %s. With the rewrite rule below it also answers at %s.',
    'LLMSTXT_ADMIN_DYNAMIC_OFF' => 'Available at %s whenever the plugin\'s enabled, whatever the output setting.',

    'LLMSTXT_ADMIN_NEVER' => 'Never.',
    'LLMSTXT_ADMIN_BUILT_AT' => '%s (%s).',
    'LLMSTXT_ADMIN_COUNTS' => '%d information pages, %d categories, %d brands, %d products, %d EZ-Pages.',
    'LLMSTXT_ADMIN_PENDING_NONE' => 'Nothing. The published copy matches the catalog and the settings.',
    'LLMSTXT_ADMIN_PENDING_SOME' => 'A rebuild is due: %s.',
    'LLMSTXT_ADMIN_CACHE_OK' => '%s is writable.',
    'LLMSTXT_ADMIN_CACHE_BAD' => '%s isn\'t writable. The live page still works, but every request builds afresh and the rebuild schedule can\'t be tracked.',
    'LLMSTXT_ADMIN_LAST_ERROR' => 'The last build reported: %s',

    'LLMSTXT_ADMIN_BUTTON_REGENERATE' => 'Regenerate now',
    'LLMSTXT_ADMIN_BUTTON_REPLACE' => 'Replace the existing file',
    'LLMSTXT_ADMIN_BUTTON_REMOVE' => 'Remove the generated files',
    'LLMSTXT_ADMIN_BUTTON_SETTINGS' => 'Settings',
    'LLMSTXT_ADMIN_BUTTON_OPEN' => 'Open the live page',
    'LLMSTXT_ADMIN_BUTTON_OPEN_FILE' => 'Open /llms.txt',
    'LLMSTXT_ADMIN_CONFIRM_REPLACE' => 'Replace the llms.txt that\'s already at the store root with the generated one? The existing file will be overwritten.',
    'LLMSTXT_ADMIN_CONFIRM_REMOVE' => 'Remove llms.txt and llms-full.txt from the store root? Only files written by this plugin are removed.',

    'LLMSTXT_ADMIN_REWRITE_INTRO' => 'If you\'d rather not have a file at the store root, or the root isn\'t writable, one line in the store\'s .htaccess makes /llms.txt (and /llms-full.txt) answer from the live page instead. Put it after <code>RewriteEngine On</code>; the store\'s own rules, if any, aren\'t affected.',
    'LLMSTXT_ADMIN_REWRITE_NOTE' => 'A file at the root wins over the rewrite rule on most servers, so with the output set to "both" the file is what visitors get and the live page is the fallback.',

    'LLMSTXT_ADMIN_PREVIEW_INTRO' => 'Built fresh for this page from the current catalog and settings. This is what the next rebuild will publish; it\'s not the published copy itself.',
    'LLMSTXT_ADMIN_PREVIEW_SIZE' => '%s characters.',

    'LLMSTXT_ADMIN_SUCCESS_REBUILT' => 'Rebuilt: %s',
    'LLMSTXT_ADMIN_SUCCESS_WROTE' => 'Written: %s.',
    'LLMSTXT_ADMIN_SUCCESS_REMOVED' => 'Removed: %s.',
    'LLMSTXT_ADMIN_NOTHING_REMOVED' => 'Nothing to remove.',
    'LLMSTXT_ADMIN_SKIPPED' => 'Skipped: %s.',
    'LLMSTXT_ADMIN_ERROR_BUILD' => 'The build didn\'t complete: %s',
    'LLMSTXT_ADMIN_ERROR_DISABLED' => 'The plugin\'s disabled under Configuration, so nothing was published. The preview below still shows what it would produce.',
];

return $define;
