<?php
/**
 * LLMs.txt Generator -- admin page (Tools -> LLMs.txt Generator).
 *
 * Reached as admin/index.php?cmd=llms_txt, the routing Zen Cart has used for
 * plugin admin pages since v1.5.7. Access is governed by the `toolsLlmsTxt`
 * row the installer writes into admin_pages, checked during admin bootstrap.
 *
 * Every state-changing operation is a POST carrying `action`, so Zen Cart's
 * global admin CSRF check in init_sessions.php applies; the token is checked
 * again here. Reads never change anything: the preview is built into memory
 * and thrown away.
 *
 * @package  LlmsTxt
 * @license  http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 */

require 'includes/application_top.php';

// By its own location, not by a path with the version in it: the version
// directory is renamed at every release and a stale string here would be a
// fatal on the first admin visit after an upgrade.
require_once dirname(__DIR__) . '/shared/generator.php';

/**
 * HTML-escape for output. Zen Cart's zen_output_string_protected() is the
 * same thing; this is one short name for a page full of them.
 */
if (!function_exists('llmstxt_h')) {
    function llmstxt_h($s)
    {
        return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
    }
}

/**
 * "3 minutes ago", for a timestamp.
 */
if (!function_exists('llmstxt_ago')) {
    function llmstxt_ago($timestamp)
    {
        $delta = max(0, time() - (int)$timestamp);
        if ($delta < 60) {
            return 'just now';
        }
        if ($delta < 3600) {
            $n = (int)floor($delta / 60);
            return $n . ' minute' . ($n === 1 ? '' : 's') . ' ago';
        }
        if ($delta < 86400) {
            $n = (int)floor($delta / 3600);
            return $n . ' hour' . ($n === 1 ? '' : 's') . ' ago';
        }
        $n = (int)floor($delta / 86400);
        return $n . ' day' . ($n === 1 ? '' : 's') . ' ago';
    }
}

$llmstxtSelf = zen_href_link(FILENAME_LLMS_TXT, '', 'SSL');

// -----
// Actions. Everything here arrives by POST.
//
$action = isset($_POST['action']) ? (string)$_POST['action'] : '';

if ($action !== '') {
    $postedToken = isset($_POST['securityToken']) ? (string)$_POST['securityToken'] : '';
    if (empty($_SESSION['securityToken']) || !hash_equals((string)$_SESSION['securityToken'], $postedToken)) {
        zen_redirect($llmstxtSelf);
    }

    switch ($action) {
        case 'rebuild':
        case 'takeover':
            $settings = llmstxt_settings();
            if (!$settings['enabled']) {
                $messageStack->add_session(LLMSTXT_ADMIN_ERROR_DISABLED, 'caution');
                break;
            }
            $result = llmstxt_publish($db, ($action === 'takeover') ? 'admin: replace' : 'admin', ($action === 'takeover'));
            if ($result['ok']) {
                $c = $result['counts'];
                $messageStack->add_session(
                    sprintf(LLMSTXT_ADMIN_SUCCESS_REBUILT, sprintf(LLMSTXT_ADMIN_COUNTS, (int)$c['info_pages'], (int)$c['categories'], (int)($c['brands'] ?? 0), (int)$c['products'], (int)$c['ezpages'])),
                    'success'
                );
                if ($result['wrote'] !== []) {
                    $messageStack->add_session(sprintf(LLMSTXT_ADMIN_SUCCESS_WROTE, implode(', ', array_map('basename', $result['wrote']))), 'success');
                }
                zen_record_admin_activity('LLMs.txt Generator: rebuilt from the admin (' . $action . ').', 'info');
            } else {
                $messageStack->add_session(sprintf(LLMSTXT_ADMIN_ERROR_BUILD, $result['error']), 'error');
            }
            foreach ($result['skipped'] as $note) {
                $messageStack->add_session(sprintf(LLMSTXT_ADMIN_SKIPPED, $note), 'caution');
            }
            break;

        case 'remove':
            $removed = llmstxt_remove_files();
            if ($removed === []) {
                $messageStack->add_session(LLMSTXT_ADMIN_NOTHING_REMOVED, 'caution');
            } else {
                $messageStack->add_session(sprintf(LLMSTXT_ADMIN_SUCCESS_REMOVED, implode(', ', $removed)), 'success');
                zen_record_admin_activity('LLMs.txt Generator: removed ' . implode(', ', $removed) . ' from the store root.', 'warning');
            }
            break;
    }

    zen_redirect($llmstxtSelf);
}

// -----
// Everything the page shows.
//
$settings = llmstxt_settings();
$state = llmstxt_read_state();
$now = time();

$fileStatus = llmstxt_file_status(false, $state);
$fullStatus = llmstxt_file_status(true, $state);
$wantsFile = ($settings['output'] === 'file' || $settings['output'] === 'both');

$cacheDir = llmstxt_cache_dir();
$cacheWritable = (is_dir($cacheDir) && is_writable($cacheDir));

$pendingReason = '';
if ($settings['enabled']) {
    $fingerprint = null;
    if ($settings['rebuild'] === 'change') {
        try {
            $fingerprint = llmstxt_fingerprint($db);
        } catch (Throwable $e) {
            $fingerprint = null;
        }
    }
    $pendingReason = llmstxt_rebuild_reason($state, $settings, $fingerprint, $now);
    if ($pendingReason === '' && $wantsFile && (int)$state['built'] > 0 && !$fileStatus['exists'] && $fileStatus['ours']) {
        $pendingReason = 'file missing';
    }
}

$liveUrl = llmstxt_link('llms_txt');
$rootUrl = llmstxt_catalog_base() . 'llms.txt';

$previewError = '';
$preview = '';
$previewCounts = ['info_pages' => 0, 'categories' => 0, 'brands' => 0, 'products' => 0, 'ezpages' => 0];
try {
    $built = llmstxt_build($db, $settings);
    $preview = $built['llms'];
    $previewCounts = $built['counts'];
} catch (Throwable $e) {
    $previewError = $e->getMessage();
}

$configGroup = $db->Execute(
    "SELECT configuration_group_id
       FROM " . TABLE_CONFIGURATION_GROUP . "
      WHERE configuration_group_title = 'LLMs.txt Generator'
      LIMIT 1"
);
$settingsUrl = $configGroup->EOF
    ? zen_href_link(FILENAME_CONFIGURATION, '', 'SSL')
    : zen_href_link(FILENAME_CONFIGURATION, 'gID=' . (int)$configGroup->fields['configuration_group_id'], 'SSL');

$outputLabels = [
    'file' => LLMSTXT_ADMIN_OUTPUT_FILE,
    'dynamic' => LLMSTXT_ADMIN_OUTPUT_DYNAMIC,
    'both' => LLMSTXT_ADMIN_OUTPUT_BOTH,
];
$rebuildLabels = [
    'change' => LLMSTXT_ADMIN_REBUILD_CHANGE,
    'daily' => LLMSTXT_ADMIN_REBUILD_DAILY,
    'weekly' => LLMSTXT_ADMIN_REBUILD_WEEKLY,
    'monthly' => LLMSTXT_ADMIN_REBUILD_MONTHLY,
    'manual' => LLMSTXT_ADMIN_REBUILD_MANUAL,
];

/**
 * One row of the status table. The value is trusted HTML assembled below
 * from escaped pieces.
 */
function llmstxt_status_row($label, $valueHtml)
{
    return '<tr><th scope="row">' . llmstxt_h($label) . '</th><td>' . $valueHtml . '</td></tr>' . "\n";
}

function llmstxt_file_row(array $status, $wanted, $enabled, $pendingReason)
{
    if ($status['exists']) {
        $text = sprintf(LLMSTXT_ADMIN_FILE_PRESENT, number_format((float)$status['size']), date('Y-m-d H:i', $status['modified']) . ', ' . llmstxt_ago($status['modified']));
        if (!$status['ours']) {
            return '<span class="llmstxt-warn">' . llmstxt_h($text) . '</span> ' . llmstxt_h(LLMSTXT_ADMIN_FILE_FOREIGN);
        }
        $html = '<span class="llmstxt-ok">' . llmstxt_h($text) . '</span>';
        if ($wanted && $enabled && $pendingReason !== '') {
            $html .= ' ' . llmstxt_h(LLMSTXT_ADMIN_FILE_STALE);
        }
        return $html;
    }
    if (!$wanted) {
        return llmstxt_h(LLMSTXT_ADMIN_FILE_NOT_WANTED);
    }
    if (!$status['writable']) {
        return '<span class="llmstxt-bad">' . llmstxt_h(LLMSTXT_ADMIN_FILE_ABSENT) . '</span> ' . llmstxt_h(LLMSTXT_ADMIN_FILE_UNWRITABLE);
    }
    return '<span class="llmstxt-warn">' . llmstxt_h($enabled ? LLMSTXT_ADMIN_FILE_ABSENT_EXPECTED : LLMSTXT_ADMIN_FILE_ABSENT) . '</span>';
}
?>
<!DOCTYPE html>
<html <?= HTML_PARAMS; ?>>
<head>
<?php require DIR_WS_INCLUDES . 'admin_html_head.php'; ?>
</head>
<body>
<!-- header //-->
<?php require DIR_WS_INCLUDES . 'header.php'; ?>
<!-- header_eof //-->

<main class="container-fluid llmstxt-page" aria-labelledby="llmstxtPageHeading">
    <h1 id="llmstxtPageHeading"><?= llmstxt_h(HEADING_TITLE); ?></h1>
    <p class="llmstxt-lead"><?= llmstxt_h(LLMSTXT_ADMIN_LEAD); ?></p>

    <h2><?= llmstxt_h(LLMSTXT_ADMIN_SECTION_STATUS); ?></h2>
    <table class="llmstxt-status">
        <tbody>
<?php
echo llmstxt_status_row(
    LLMSTXT_ADMIN_ROW_ENABLED,
    $settings['enabled']
        ? '<span class="llmstxt-ok">' . llmstxt_h(LLMSTXT_ADMIN_ENABLED) . '</span>'
        : '<span class="llmstxt-bad">' . llmstxt_h(LLMSTXT_ADMIN_DISABLED) . '</span>'
);
echo llmstxt_status_row(LLMSTXT_ADMIN_ROW_OUTPUT, llmstxt_h($outputLabels[$settings['output']]));
echo llmstxt_status_row(
    LLMSTXT_ADMIN_ROW_REBUILD,
    llmstxt_h($rebuildLabels[$settings['rebuild']])
    . ($settings['rebuild'] !== 'manual' ? '<br><small>' . llmstxt_h(LLMSTXT_ADMIN_REBUILD_NOTE) . '</small>' : '')
);
echo llmstxt_status_row(LLMSTXT_ADMIN_ROW_FILE, llmstxt_file_row($fileStatus, $wantsFile, $settings['enabled'], $pendingReason));
if ($settings['full'] || $fullStatus['exists']) {
    echo llmstxt_status_row(LLMSTXT_ADMIN_ROW_FULL, llmstxt_file_row($fullStatus, $wantsFile && $settings['full'], $settings['enabled'], $pendingReason));
}
echo llmstxt_status_row(
    LLMSTXT_ADMIN_ROW_DYNAMIC,
    ($settings['output'] === 'dynamic' || $settings['output'] === 'both')
        ? sprintf(LLMSTXT_ADMIN_DYNAMIC_ON, '<code>' . llmstxt_h($liveUrl) . '</code>', '<code>' . llmstxt_h($rootUrl) . '</code>')
        : sprintf(LLMSTXT_ADMIN_DYNAMIC_OFF, '<code>' . llmstxt_h($liveUrl) . '</code>')
);
echo llmstxt_status_row(
    LLMSTXT_ADMIN_ROW_LAST_BUILT,
    ((int)$state['built'] > 0)
        ? llmstxt_h(sprintf(LLMSTXT_ADMIN_BUILT_AT, date('Y-m-d H:i', (int)$state['built']) . ', ' . llmstxt_ago((int)$state['built']), (string)$state['last_reason']))
        : llmstxt_h(LLMSTXT_ADMIN_NEVER)
);
if (!empty($state['counts'])) {
    $c = $state['counts'];
    echo llmstxt_status_row(
        LLMSTXT_ADMIN_ROW_CONTENTS,
        // A state file written before brands were counted has no 'brands' key.
        llmstxt_h(sprintf(LLMSTXT_ADMIN_COUNTS, (int)$c['info_pages'], (int)$c['categories'], (int)($c['brands'] ?? 0), (int)$c['products'], (int)$c['ezpages']))
    );
}
if ($settings['enabled']) {
    echo llmstxt_status_row(
        LLMSTXT_ADMIN_ROW_PENDING,
        ($pendingReason === '')
            ? '<span class="llmstxt-ok">' . llmstxt_h(LLMSTXT_ADMIN_PENDING_NONE) . '</span>'
            : '<span class="llmstxt-warn">' . llmstxt_h(sprintf(LLMSTXT_ADMIN_PENDING_SOME, $pendingReason)) . '</span>'
    );
}
echo llmstxt_status_row(
    LLMSTXT_ADMIN_ROW_CACHE,
    $cacheWritable
        ? llmstxt_h(sprintf(LLMSTXT_ADMIN_CACHE_OK, $cacheDir))
        : '<span class="llmstxt-bad">' . llmstxt_h(sprintf(LLMSTXT_ADMIN_CACHE_BAD, $cacheDir)) . '</span>'
);
if ((string)$state['last_error'] !== '') {
    echo llmstxt_status_row('', '<span class="llmstxt-bad">' . llmstxt_h(sprintf(LLMSTXT_ADMIN_LAST_ERROR, $state['last_error'])) . '</span>');
}
?>
        </tbody>
    </table>

    <h2><?= llmstxt_h(LLMSTXT_ADMIN_SECTION_ACTIONS); ?></h2>
    <div class="llmstxt-actions">
        <?= zen_draw_form('llmstxtRebuild', FILENAME_LLMS_TXT, '', 'post'); ?>
            <input type="hidden" name="action" value="rebuild">
            <button type="submit" class="btn btn-primary"><?= llmstxt_h(LLMSTXT_ADMIN_BUTTON_REGENERATE); ?></button>
        </form>
<?php if ($wantsFile && $fileStatus['exists'] && !$fileStatus['ours']) { ?>
        <?= zen_draw_form('llmstxtTakeover', FILENAME_LLMS_TXT, '', 'post', 'onsubmit="return confirm(\'' . llmstxt_h(addslashes(LLMSTXT_ADMIN_CONFIRM_REPLACE)) . '\');"'); ?>
            <input type="hidden" name="action" value="takeover">
            <button type="submit" class="btn btn-warning"><?= llmstxt_h(LLMSTXT_ADMIN_BUTTON_REPLACE); ?></button>
        </form>
<?php } ?>
<?php if (($fileStatus['exists'] && $fileStatus['ours']) || ($fullStatus['exists'] && $fullStatus['ours'])) { ?>
        <?= zen_draw_form('llmstxtRemove', FILENAME_LLMS_TXT, '', 'post', 'onsubmit="return confirm(\'' . llmstxt_h(addslashes(LLMSTXT_ADMIN_CONFIRM_REMOVE)) . '\');"'); ?>
            <input type="hidden" name="action" value="remove">
            <button type="submit" class="btn btn-default"><?= llmstxt_h(LLMSTXT_ADMIN_BUTTON_REMOVE); ?></button>
        </form>
<?php } ?>
        <a class="btn btn-default" role="button" href="<?= llmstxt_h($settingsUrl); ?>"><?= llmstxt_h(LLMSTXT_ADMIN_BUTTON_SETTINGS); ?></a>
        <a class="btn btn-default" role="button" href="<?= llmstxt_h($liveUrl); ?>" target="_blank" rel="noopener noreferrer"><?= llmstxt_h(LLMSTXT_ADMIN_BUTTON_OPEN); ?></a>
<?php if ($fileStatus['exists']) { ?>
        <a class="btn btn-default" role="button" href="<?= llmstxt_h($rootUrl); ?>" target="_blank" rel="noopener noreferrer"><?= llmstxt_h(LLMSTXT_ADMIN_BUTTON_OPEN_FILE); ?></a>
<?php } ?>
    </div>

    <h2><?= llmstxt_h(LLMSTXT_ADMIN_SECTION_REWRITE); ?></h2>
    <div class="llmstxt-note">
        <p><?= LLMSTXT_ADMIN_REWRITE_INTRO; ?></p>
        <p><?= llmstxt_h(LLMSTXT_ADMIN_REWRITE_NOTE); ?></p>
    </div>
<pre class="llmstxt-snippet">RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^llms\.txt$ index.php?main_page=llms_txt [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^llms-full\.txt$ index.php?main_page=llms_txt&amp;full=1 [L]</pre>

    <h2><?= llmstxt_h(LLMSTXT_ADMIN_SECTION_PREVIEW); ?></h2>
<?php if ($previewError !== '') { ?>
    <p class="llmstxt-bad"><?= llmstxt_h(sprintf(LLMSTXT_ADMIN_ERROR_BUILD, $previewError)); ?></p>
<?php } else { ?>
    <p><?= llmstxt_h(LLMSTXT_ADMIN_PREVIEW_INTRO); ?>
       <?= llmstxt_h(sprintf(LLMSTXT_ADMIN_COUNTS, (int)$previewCounts['info_pages'], (int)$previewCounts['categories'], (int)$previewCounts['brands'], (int)$previewCounts['products'], (int)$previewCounts['ezpages'])); ?>
       <?= llmstxt_h(sprintf(LLMSTXT_ADMIN_PREVIEW_SIZE, number_format((float)llmstxt_strlen($preview)))); ?></p>
    <pre class="llmstxt-preview"><?= llmstxt_h($preview); ?></pre>
<?php } ?>
</main>

<!-- footer //-->
<?php require DIR_WS_INCLUDES . 'footer.php'; ?>
<!-- footer_eof //-->
</body>
</html>
<?php require DIR_WS_INCLUDES . 'application_bottom.php'; ?>
