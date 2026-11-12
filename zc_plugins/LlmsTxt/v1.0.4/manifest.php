<?php
/**
 * Automatic LLMs.txt Generator -- plugin manifest.
 *
 * What is in pluginDescription is shown in the Plugin Manager's info panel as
 * raw HTML on every release from v1.5.8 to v3.0.0, so the ReadMe, Settings,
 * Forum Thread and GitHub buttons live there, styled like the panel's own
 * Install / Uninstall buttons.
 *
 * On v2.2 and later the description is rewritten from this file on every
 * Plugin Manager visit. On v1.5.8, v2.0 and v2.1 it is written by the INSERT
 * that first creates the plugin_control row, and only this plugin's installer
 * refreshes it there (at Install, Upgrade and Uninstall). The one
 * state-dependent part, the Settings button, is handled below; no setting's
 * value belongs in this file.
 *
 * ReadMe opens README.md through the plugin's own admin page
 * (index.php?cmd=llms_txt_readme): behind the admin login, superusers only,
 * in its own window. It is shown only to superusers; the page enforces that
 * too, because Zen Cart 1.5.8-2.1 keep the description they first saw. Forum
 * Thread and GitHub go off-site, so they open in a new window with
 * rel="noopener noreferrer" (zencart/zencart#8031).
 *
 * @package  LlmsTxt
 * @license  https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

$llmstxtReadmeUrl = 'index.php?cmd=llms_txt_readme';
// Zen Cart 3.0.0 reads every manifest on every admin page, early (template resolver),
// before admin access is set up: zen_is_superuser() there is a fatal "Undefined constant
// SUPERUSER_PROFILE" that takes down the whole admin. Ask only once that exists.
$llmstxtShowReadme = true;
if (function_exists('zen_is_superuser') && defined('SUPERUSER_PROFILE')) {
    $llmstxtShowReadme = zen_is_superuser();
}
$llmstxtGithubUrl = 'https://github.com/dbltoe/Automatic_LLMs_Txt_Generator';

/**
 * The Zen Cart forum support thread. SET THIS BEFORE THE FIRST RELEASE: on
 * v1.5.8, v2.0 and v2.1 a later edit reaches nobody who has already installed.
 * An empty string renders no link at all, which is right until a thread exists.
 */
$llmstxtForumUrl = 'https://www.zen-cart.com/threads/207327#post-1347041';

/**
 * The Settings button: this plugin's Configuration page, shown only once the
 * plugin is installed. Its gID is assigned at install, so it differs from
 * store to store and can't be written here; it's read from the admin page the
 * installer registers (page key configLlmsTxt). The link is relative
 * ("index.php?cmd=configuration&gID=N") because Plugin Manager is an admin
 * page, so it survives a domain or admin-folder rename.
 *
 * Zen Cart reads manifests only on the Plugin Manager page, but the lookup is
 * still guarded so a manifest read anywhere else (no database, catalog side,
 * CLI) just leaves the button out. On v2.2 and later this file is re-read on
 * every Plugin Manager visit, so the button follows the install state by
 * itself; on v1.5.8, v2.0 and v2.1 the installer saves the re-read
 * description at Install, Upgrade and Uninstall
 * (llmstxtRefreshPluginDescription).
 */
$llmstxtSettingsUrl = '';
if (defined('IS_ADMIN_FLAG') && IS_ADMIN_FLAG === true && defined('TABLE_ADMIN_PAGES')
    && isset($GLOBALS['db']) && is_object($GLOBALS['db'])) {
    $llmstxtPage = $GLOBALS['db']->Execute(
        "SELECT main_page, page_params FROM " . TABLE_ADMIN_PAGES
        . " WHERE page_key = 'configLlmsTxt' LIMIT 1"
    );
    if (!$llmstxtPage->EOF && defined($llmstxtPage->fields['main_page'])) {
        $llmstxtSettingsUrl = 'index.php?cmd=' . constant($llmstxtPage->fields['main_page'])
            . '&' . $llmstxtPage->fields['page_params'];
    }
}

/**
 * One row: ReadMe | Settings | Forum Thread | GitHub. A second line would push
 * the Author line and the core buttons further down the panel. Spacing is set
 * inline, which beats any theme `.btn` margin: every gap is 4px (the wrapper
 * supplies the space before the first button, each button the space after
 * itself) and the side padding is 8px rather than Bootstrap's 12px, so all
 * four fit on one line in Plugin Manager's side panel from a 1366px-wide
 * window up. Settings opens in the same tab (it's the admin itself); the
 * others open in a new one.
 */
$llmstxtGap = '4px';
$llmstxtButtonStyle = ' style="margin:0 ' . $llmstxtGap . ' 0 0;padding-left:8px;padding-right:8px"';
$llmstxtButton = static function ($url, $label, $newTab) use ($llmstxtButtonStyle) {
    return '<a href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '"'
        . ($newTab ? ' target="_blank" rel="noopener noreferrer"' : '')
        . ' class="btn btn-primary" role="button"' . $llmstxtButtonStyle . '>' . $label . '</a>';
};

$llmstxtLinks =
    '<div style="margin:10px 0 0;padding:0 0 0 ' . $llmstxtGap . '">'
    . ($llmstxtShowReadme ? $llmstxtButton($llmstxtReadmeUrl, 'ReadMe', true) : '')
    . ($llmstxtSettingsUrl !== '' ? $llmstxtButton($llmstxtSettingsUrl, 'Settings', false) : '')
    . ($llmstxtForumUrl !== '' ? $llmstxtButton($llmstxtForumUrl, 'Forum Thread', true) : '')
    . $llmstxtButton($llmstxtGithubUrl, 'GitHub', true)
    . '</div>';

return [
    'pluginVersion' => 'v1.0.4',
    'pluginName' => 'Automatic LLMs.txt Generator',
    'pluginDescription' =>
        'Publishes an llms.txt file for your store, the Markdown guide that AI assistants '
        . 'read to understand a website: your store name and summary, shipping and policy pages, '
        . 'categories, brands, products with prices, and EZ-Pages. Served live at /llms.txt or written as '
        . 'a real file at the store root, rebuilt automatically when the catalog changes, on a '
        . 'schedule, or only when you ask.'
        . $llmstxtLinks,
    'pluginAuthor' => 'My Zen Cart Host (dbltoe)',
    // The Zen Cart Plugins Library's Plugin ID for this plugin: 2455, assigned
    // when the Library renumbered it on 2026-10-06. NOT the number in the
    // download URL (7800 there is a release id), and not 2257, the ID used
    // through v1.0.1, which also belonged to an older plugin (Finnish Language
    // pack), so update checks reported that plugin. An ID the version server
    // does not know makes Plugin Manager fatal on Zen Cart 2.x with PHP 8.
    // Keep it a bare integer: the Library's stamper can't read a quoted value.
    // v1.5.8/v2.0/v2.1 never refresh zc_contrib_id on an existing row, so
    // this must be right in every release.
    'pluginId' => 2455,
    'zcVersions' => ['v158', 'v200', 'v210', 'v220', 'v230', 'v300'],
    'changelog' => 'changelog.txt',
    'github_repo' => $llmstxtGithubUrl,
    'pluginGroups' => [],
];
