<?php
/**
 * Automatic LLMs.txt Generator -- plugin manifest.
 *
 * What is in pluginDescription is shown in the Plugin Manager's info panel as
 * raw HTML on every release from v1.5.8 to v3.0.0, so the Read Me and GitHub
 * buttons live there, styled like the panel's own Install / Uninstall buttons.
 *
 * Nothing state-dependent belongs in this file: on v1.5.8, v2.0 and v2.1 the
 * description is written by the INSERT that first creates the plugin_control
 * row and never refreshed. What a store sees on its first scan is what it
 * keeps until an uninstall and re-install.
 *
 * @package  LlmsTxt
 * @license  http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 */

$llmstxtPluginDir = 'zc_plugins/LlmsTxt/v1.0.1/';
$llmstxtReadmeUrl = (defined('DIR_WS_CATALOG') ? DIR_WS_CATALOG : '/') . $llmstxtPluginDir . 'readme.html';
$llmstxtGithubUrl = 'https://github.com/dbltoe/Automatic_LLMs_Txt_Generator';

/**
 * The Zen Cart forum support thread. SET THIS BEFORE THE FIRST RELEASE: on
 * v1.5.8, v2.0 and v2.1 a later edit reaches nobody who has already installed.
 * An empty string renders no link at all, which is right until a thread exists.
 */
$llmstxtForumUrl = 'https://www.zen-cart.com/threads/207327#post-1347041';

$llmstxtGap = '6px';
$llmstxtLinks =
    '<div style="margin:10px 0 0;padding:0 0 0 ' . $llmstxtGap . '">'
    . '<a href="' . $llmstxtReadmeUrl . '" target="_blank" rel="noopener noreferrer"'
    . ' class="btn btn-primary" role="button" style="margin:0 ' . $llmstxtGap . ' 0 0">Read Me</a>'
    . '<a href="' . $llmstxtGithubUrl . '" target="_blank" rel="noopener noreferrer"'
    . ' class="btn btn-primary" role="button" style="margin:0 ' . $llmstxtGap . ' 0 0">GitHub</a>'
    . '</div>';

$llmstxtForumLink = '';
if ($llmstxtForumUrl !== '') {
    $llmstxtForumLink =
        '<div style="margin:8px 0 0;padding:0 0 0 ' . $llmstxtGap . '">'
        . '<a href="' . $llmstxtForumUrl . '" target="_blank" rel="noopener noreferrer">Forum Support Thread</a>'
        . '</div>';
}

return [
    'pluginVersion' => 'v1.0.1',
    'pluginName' => 'Automatic LLMs.txt Generator',
    'pluginDescription' =>
        'Publishes an llms.txt file for your store, the Markdown guide that AI assistants '
        . 'read to understand a website: your store name and summary, shipping and policy pages, '
        . 'categories, brands, products with prices, and EZ-Pages. Served live at /llms.txt or written as '
        . 'a real file at the store root, rebuilt automatically when the catalog changes, on a '
        . 'schedule, or only when you ask.'
        . $llmstxtLinks
        . $llmstxtForumLink,
    'pluginAuthor' => 'My Zen Cart Host (dbltoe)',
    // The Zen Cart Plugins Library id: the value the Library itself wrote into
    // the manifest of the accepted v1.0.0 package. NOT the number in the
    // download URL (7800 there is the release id): an id the version server
    // does not know makes Plugin Manager fatal on Zen Cart 2.x with PHP 8.
    // v1.5.8/v2.0/v2.1 never refresh zc_contrib_id on an existing row, so
    // this must be right in every release.
    'pluginId' => 2257,
    'zcVersions' => ['v158', 'v200', 'v210', 'v220', 'v230', 'v300'],
    'changelog' => 'changelog.txt',
    'github_repo' => $llmstxtGithubUrl,
    'pluginGroups' => [],
];
