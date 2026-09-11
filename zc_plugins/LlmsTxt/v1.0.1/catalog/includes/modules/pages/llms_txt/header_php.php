<?php
/**
 * Automatic LLMs.txt Generator -- the storefront page that serves the file.
 *
 * Reached as index.php?main_page=llms_txt (or, with the rewrite rule from the
 * readme, as /llms.txt). Zen Cart's PageLoader finds a plugin's
 * catalog/includes/modules/pages/PAGE directory on every supported release,
 * v1.5.8 included, so no file is copied into the store's own includes/.
 *
 * A "page" in Zen Cart would normally go on to render the template. This one
 * has nothing to render: it sends the text and stops, the same way sitemap
 * plugins do. The session has been started and will be written on shutdown.
 *
 * @package  LlmsTxt
 * @license  http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 */

if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
}

require_once dirname(__DIR__, 5) . '/shared/generator.php';

$llmstxtFull = (isset($_GET['full']) && (string)$_GET['full'] === '1');
$llmstxtServed = llmstxt_serve($db, $llmstxtFull);

if ((int)$llmstxtServed['status'] !== 200) {
    header('HTTP/1.1 ' . ((int)$llmstxtServed['status'] === 500 ? '500 Internal Server Error' : '404 Not Found'));
    header('Content-Type: text/plain; charset=utf-8');
    echo ((int)$llmstxtServed['status'] === 500) ? "llms.txt could not be generated.\n" : "Not found.\n";
    exit;
}

header('Content-Type: text/plain; charset=utf-8');
header('Cache-Control: public, max-age=3600');
header('X-Content-Type-Options: nosniff');
header('Content-Length: ' . strlen($llmstxtServed['body']));
echo $llmstxtServed['body'];
exit;
