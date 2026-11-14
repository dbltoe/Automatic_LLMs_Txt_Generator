# Automatic LLMs.txt Generator

Version 1.0.4 · Zen Cart 1.5.8 through 3.0.0 · PHP 7.4 through 8.5 · GPL v2 · by My Zen Cart Host (dbltoe)

Publish an llms.txt for your store: the short Markdown guide that AI assistants read to understand a website, built from your own catalog and kept current on its own.

## What it does

An `llms.txt` file is to AI assistants what `sitemap.xml` is to search engines: a plain file at the root of your site that says what the site is and where the important pages are, in Markdown, following the llmstxt.org convention (https://llmstxt.org/). This plugin writes one for your store from what is already in Zen Cart, and keeps it current.

The file it produces looks like this:

```
# Acme Widgets

> Acme Widgets sells replacement parts and tools for vintage kitchen
> mixers, shipping from Texas to the US and Canada.

- [Website](https://www.example.com/)
- Prices are in: USD
- [Sitemap](https://www.example.com/sitemap.xml)
- [Robots](https://www.example.com/robots.txt)

## Shopping information

- [Shipping and Returns](https://www.example.com/index.php?main_page=shippinginfo)
- [Privacy Notice](https://www.example.com/index.php?main_page=privacy)
- [Conditions of Use](https://www.example.com/index.php?main_page=conditions)
- [Contact Us](https://www.example.com/index.php?main_page=contact_us)

## Categories

- [Mixer Parts](https://www.example.com/index.php?main_page=index&cPath=1): Gears, gaskets and beaters for every model since 1937.
- [Mixer Parts > Gaskets](https://www.example.com/index.php?main_page=index&cPath=1_4)

## Brands

- [Hobart](https://www.example.com/index.php?main_page=index&manufacturers_id=2): 14 products

## Products

- [Planetary Gear, Model K](https://www.example.com/index.php?main_page=product_info&products_id=12): Bronze replacement gear for the Model K. Fits 1948 to 1962. ($24.95)

## More pages

- [About Us](https://www.example.com/index.php?main_page=page&id=3)
```

Every part of that comes from somewhere you already maintain: the store name, the shipping and policy pages, the category tree, your brands with a product count each, enabled products with their meta-tag or body descriptions and base prices, and your published EZ-Pages. The sitemap and `robots.txt` are linked when the store has them. The one thing worth writing by hand is the summary in the blockquote, because it's the first sentence an assistant reads. There is a setting for it.

Names and descriptions are Markdown-escaped on the way out, so a product called "50% off [Sale]" or a brand called "Model_K" reads as written instead of turning into a broken link or stray italics. Your own summary and notes are the exception: they're passed through as you wrote them, Markdown included.

Optionally it also produces `llms-full.txt`, the same sections with complete descriptions, for assistants that want the detail.

## Installing

1. Upload the `zc_plugins/LlmsTxt/` directory so it lands at `<your store root>/zc_plugins/LlmsTxt/v1.0.4/`. Nothing goes anywhere else: no files in `admin/`, `includes/` or your template.
2. In your admin, go to **Modules → Plugin Manager**, select **Automatic LLMs.txt Generator** and press **Install**.

The installer creates a configuration group under **Configuration → Automatic LLMs.txt Generator**, adds **Automatic LLMs.txt Generator** to the **Tools** menu, and writes a first `llms.txt` at the store root straight away if it can. If the root isn't writable it says so and the Tools page explains the alternative.

Once it's installed, a superuser can open this guide from the **ReadMe** button in Plugin Manager.

> **If your store is a git checkout of Zen Cart:** `zc_plugins/.gitignore` uses a deny-all rule with an allowlist, so add `!LlmsTxt/` and `!LlmsTxt/**` to it or git won't see the plugin. This doesn't affect the plugin working.

## Quick start

1. Open **Configuration → Automatic LLMs.txt Generator** and write one or two sentences in **Store summary**: what you sell and who it's for.
2. Open **Tools → Automatic LLMs.txt Generator**. The status table says whether the file is present and the preview shows exactly what will be published. Press **Regenerate now**.
3. Open `https://your-store/llms.txt` in a browser. That's what an assistant will read.

That's all a typical store needs. The defaults list every reachable category, the first 200 products in their sort order with prices, the standard information pages, and your EZ-Pages, and they rebuild whenever the catalog changes.

## Two ways to publish

The **Publish as** setting decides where the file comes from.

| Setting | What happens | Needs |
| --- | --- | --- |
| file | A real `llms.txt` is written at the store root, next to `robots.txt`. This is the default and works with no other setup. | The store root writable by the web server. It usually is on a cPanel host. |
| dynamic | The file is served live by the storefront page `index.php?main_page=llms_txt`. Nothing is written at the root. | Three lines in `.htaccess` so that `/llms.txt` reaches that page. The Tools page shows them. |
| both | The file is written, and the live page stays available as a fallback. | Both of the above. |

The live page exists whenever the plugin is enabled, whatever this setting says, so you can always look at `index.php?main_page=llms_txt` to see the current output. It keeps a rendered copy in the store's `cache/` directory and rebuilds it on the same rules as the file, so a crawler never waits for a build.

### The rewrite rule

For **dynamic** (or as a fallback with **both**), add these lines to the `.htaccess` at your store root, after `RewriteEngine On` if you already have one:

```
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^llms\.txt$ index.php?main_page=llms_txt [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^llms-full\.txt$ index.php?main_page=llms_txt&full=1 [L]
```

The `!-f` condition means a real file at the root always wins, so switching between the modes later needs no change to these lines. If your store lives in a subdirectory, the rule still works because `.htaccess` rules are relative to their own directory.

## When it rebuilds

The **Rebuild** setting is what you asked for when you asked for a file that stays current without anybody remembering to press a button.

| Setting | Rebuilds when |
| --- | --- |
| change | A product, category or EZ-Page is saved in the admin. The storefront also compares a fingerprint of the catalog (counts and last-modified times) once every fifteen minutes and rebuilds if it has moved, which catches deletions and status changes made from listings. This is the default. |
| daily / weekly / monthly | That long after the previous build, and also after an admin save. |
| manual | Only when you press **Regenerate now** on the Tools page. |

Two things are true for every setting: changing any setting in the group triggers a rebuild, and the actual work happens on the next *storefront* visit rather than in the admin, at most once every fifteen minutes. An admin save only sets a flag, so saving a product is as fast as it ever was. If nobody visits the storefront, nobody needs a fresh file either.

Zen Cart has no scheduler of its own, which is why the storefront does this. If you would rather drive it from a real cron job, request `index.php?main_page=llms_txt` from cron with `curl` or `wget`; that request performs the same check.

## Settings

All under **Configuration → Automatic LLMs.txt Generator**.

| Setting | Default | Notes |
| --- | --- | --- |
| Enable LLMs.txt | true | Off: the live page answers 404 and no file is written or updated. A file already at the root is left as it is. |
| Publish as | file | See Two ways to publish. |
| Rebuild | change | See When it rebuilds. |
| Store summary | empty | The blockquote. Write this one. Left empty, "*Store Name* is an online store." is used. |
| Additional notes | empty | Paragraphs after the summary: where you ship, delivery times, minimum orders. Markdown allowed, passed through as written. |
| Information pages | shippinginfo, privacy, conditions, contact_us, site_map | Page names, comma-separated, in order. Built-in pages get a label automatically; give your own after a colon: `about_us:About Our Company`. Empty drops the section. |
| List categories | true | Each category as "Parent > Child" with its description. Disabled categories, and everything beneath them, are skipped. |
| Category depth | 0 | 0 lists every level; 1 lists top-level categories only; and so on. |
| Exclude categories | empty | Category ids to leave out along with their subcategories and every product whose master category is one of them. Handy for a gift-certificate or clearance category. |
| List products | true | Only enabled products whose master category is reachable. |
| Maximum products | 200 | 0 means all. The order setting decides which products make the cut. |
| Product order | sort | sort (the product sort order, then name), newest, bestsellers, name, viewed. |
| Show prices | true | The base price in the default currency. Specials and tax are not applied. |
| Description length | 160 | Characters per line. The meta-tag description is used when written; otherwise the start of the body text with HTML removed. |
| List brands | true | Manufacturers with at least one enabled product, each linked to its product listing with a count. A brand whose products all sit in hidden or excluded categories is left out. |
| List EZ-Pages | true | Published pages that appear in the header, a sidebox, the footer or the table of contents. External-link pages point where they point. |
| Sitemap address | empty | Left empty, a `sitemap.xml` at the root is linked automatically when one exists. A `robots.txt` at the root is linked the same way; there is no setting for it. |
| Language | empty | Two-letter code. Empty uses the store default. |
| Also publish llms-full.txt | false | A second, longer file with complete descriptions and model numbers, linked from llms.txt. |
| Full description length | 1200 | Characters per item in llms-full.txt. |

## The Tools page

**Tools → Automatic LLMs.txt Generator** shows, in one table: whether the plugin is enabled, how it publishes, when it rebuilds, whether the file at the root is present and was written by this plugin, when the last build ran and what it found, whether a rebuild is pending and why, and whether the cache directory is writable. Below that:

- **Regenerate now** builds and publishes immediately.
- **Replace the existing file** appears only when an `llms.txt` that this plugin did not write is at the root. See the next section.
- **Remove the generated files** deletes `llms.txt` and `llms-full.txt` from the root, if this plugin wrote them.
- **Settings**, **Open the live page** and **Open /llms.txt** go where they say.

The preview at the bottom is built fresh every time the page loads, from the current catalog and settings, so it always shows what the next build will publish.

## If you already have an llms.txt

A hand-written `llms.txt` at the store root is never overwritten. The plugin recognizes its own files by a marker line at the end and by the hash it recorded when it wrote them; anything else is left alone, the Tools page says so, and the automatic rebuilds skip the file until you decide. Press **Replace the existing file** to let the generated one take over, or switch **Publish as** to **dynamic** and keep your file, since a real file wins over the rewrite rule.

## SEO URL plugins

When the file is built from the storefront, which is where every automatic rebuild happens, links are made with `zen_href_link()`, so an SEO URL plugin shapes them exactly as it shapes every other link on the site. A build triggered from the admin (Regenerate, or the install) uses plain `index.php?main_page=` addresses, because the admin's own link function builds admin addresses. Both forms resolve on a store with such a plugin, and the next storefront rebuild replaces the plain ones.

## robots.txt

Nothing needs adding, and if you have one, `llms.txt` links to it. If your `robots.txt` disallows `index.php` or query strings for some crawlers, they may not be allowed to fetch the live page; a real file at the root is unaffected by such rules, which is one reason **file** is the default.

## Translating

The words that appear in the generated file (section headings, "Website", "Prices are in", the labels for the built-in information pages) come from two language files, one per side, because the file can be built from either:

```
catalog/includes/languages/english/extra_definitions/lang.llms_txt.php
admin/includes/languages/english/extra_definitions/lang.llms_txt_names.php
```

Copy each into the matching directory for your language inside this plugin and translate the values. The admin page's own strings are in `admin/includes/languages/english/lang.llms_txt.php`. Every file uses the array format that Zen Cart v1.5.8 through v3.0.0 understand.

## Upgrading

Upload the new version directory beside the old one and press **Upgrade** in Plugin Manager. Settings you have changed are kept; only labels, help text and defaults for new settings are refreshed. Then remove the old version directory.

**Version 1.0.4** replaces the old HTML readme with this guide: the ReadMe button in Plugin Manager now opens it inside the admin, for superusers, in its own window. Nothing changes in the llms.txt file or in the settings.

## Uninstalling

Plugin Manager's **Uninstall** removes the configuration group, the menu entries and the cache. The generated `llms.txt` at the store root is left in place, on purpose: it's a public file that assistants may already know about, and it keeps working without the plugin. Press **Remove the generated files** on the Tools page first if you want it gone.

## Troubleshooting

### The Tools page says the store root isn't writable

The web server can't create files in the directory that holds `index.php`. On a cPanel host with suPHP or LSAPI it normally can; on other hosts make the directory group-writable or ask your host. Or switch **Publish as** to **dynamic** and add the rewrite rule: no file is needed then.

### /llms.txt gives a 404 in dynamic mode

The rewrite rule is missing or sits before `RewriteEngine On`. Check that `index.php?main_page=llms_txt` works first; if it does, the rule is the only thing left.

### The file never updates

Check **Rebuild** isn't **manual**, then look at the **Pending** row on the Tools page. If it says a rebuild is due, the storefront simply has not been visited since; open any storefront page and look again. If the **Cache directory** row is red, the plugin can't record when it last checked, so make `cache/` writable, which Zen Cart needs anyway.

### A product is missing

It's disabled, its master category or one of that category's parents is disabled or excluded, it has no name in the chosen language, or it fell outside **Maximum products** under the chosen order. The preview on the Tools page reflects all of those immediately.

### The links are index.php addresses although the store uses SEO URLs

The last build ran from the admin. See SEO URL plugins; the next storefront rebuild fixes it, or press nothing and wait for one.

## Support

Source, issues and releases are on GitHub (https://github.com/dbltoe/Automatic_LLMs_Txt_Generator). Questions and problem reports go to the Zen Cart forum support thread (https://www.zen-cart.com/threads/207327#post-1347041), which is also linked from the plugin's Plugin Manager panel.

## Changelog

See `CHANGELOG.md`. Version 1.0.4 replaces the old HTML readme with this guide, opened from the ReadMe button in Plugin Manager.

## License

Automatic LLMs.txt Generator is free software under the GNU General Public License, version 2 (https://www.gnu.org/licenses/old-licenses/gpl-2.0.html); the full text is in `LICENSE`. Copyright © 2026 My Zen Cart Host (dbltoe).
