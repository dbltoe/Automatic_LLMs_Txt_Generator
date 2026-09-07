# Configuring LLMs.txt Generator

All settings are under **Configuration → LLMs.txt Generator**. The Tools page
(**Tools → LLMs.txt Generator**) shows the result of them.

---

## Publishing

| Setting | Default | What it does |
|---|---|---|
| Enable LLMs.txt | true | Off: the live page answers 404 and no file is written or updated. A file already at the root is left as it is. |
| Publish as | file | **file**: a real `llms.txt` at the store root. **dynamic**: served by `index.php?main_page=llms_txt`, with a rewrite rule so `/llms.txt` reaches it. **both**: the file, with the live page as a fallback. |
| Rebuild | change | **change**: after an admin save of a product, category or EZ-Page, and whenever a fifteen-minute storefront check finds the catalog fingerprint has moved. **daily / weekly / monthly**: on that schedule, plus after an admin save. **manual**: only from the Regenerate button. |

### The rewrite rule, for dynamic output

Add to the `.htaccess` at the store root, after `RewriteEngine On`:

```
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^llms\.txt$ index.php?main_page=llms_txt [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^llms-full\.txt$ index.php?main_page=llms_txt&full=1 [L]
```

The `!-f` condition means a real file at the root always wins, so switching
modes later needs no change to these lines.

### Where the work happens

Zen Cart has no scheduler, so automatic rebuilds run on a storefront visit,
never in the admin. An admin save only sets a flag; the next storefront request
after the fifteen-minute check interval does the build, and the storefront's
own visitor is never made to wait for it because the page being served isn't
`llms.txt` itself. The live page keeps a rendered copy in `cache/` and rebuilds
it on the same rules, so a crawler is always served a finished file.

If you'd rather drive this from a real cron job, request
`index.php?main_page=llms_txt` with `curl` or `wget` on whatever schedule you
like. That request performs the same check.

---

## Content

| Setting | Default | What it does |
|---|---|---|
| Store summary | empty | The blockquote under the title. Write this one. Left empty, "*Store Name* is an online store." is used. |
| Additional notes | empty | Paragraphs after the summary: where you ship, delivery times, minimum orders. Markdown allowed, passed through as written. |
| Information pages | shippinginfo, privacy, conditions, contact_us, site_map | Page names, comma-separated, in order. Built-in pages get a label automatically; give your own after a colon, e.g. `about_us:About Our Company`. Empty drops the section. |
| List categories | true | Each category as "Parent > Child" with its description. Disabled categories, and everything beneath them, are skipped. |
| Category depth | 0 | 0 lists every level; 1 lists top-level categories only; and so on. |
| Exclude categories | empty | Category ids to leave out, with their subcategories and every product whose master category is one of them. The id is shown in the admin category listing. |
| List products | true | Enabled products whose master category is reachable. |
| Maximum products | 200 | 0 means all. The order setting decides which products make the cut. |
| Product order | sort | **sort** (the product sort order, then name), **newest**, **bestsellers**, **name**, **viewed**. |
| Show prices | true | The base price in the default currency. Specials and tax aren't applied. |
| Description length | 160 | Characters per line. The meta-tag description is used when written; otherwise the start of the body text with HTML removed. |
| List EZ-Pages | true | Published pages that appear in the header, a sidebox, the footer or the table of contents. External-link pages point where they point. |
| Sitemap address | empty | Left empty, a `sitemap.xml` at the root is linked automatically when one exists. |
| Language | empty | Two-letter code. Empty uses the store default. |
| Also publish llms-full.txt | false | A second, longer file with complete descriptions and model numbers, linked from `llms.txt`. |
| Full description length | 1200 | Characters per item in `llms-full.txt`. |

Changing any setting triggers a rebuild on the next check.

---

## A file you wrote yourself

A hand-written `llms.txt` at the store root is never overwritten. The plugin
recognizes its own files by a marker line at the end and by the hash it
recorded when writing them; anything else is left alone, the Tools page says
so, and automatic rebuilds skip the file until you decide. Press **Replace the
existing file** on the Tools page to let the generated one take over, or set
**Publish as** to **dynamic** and keep yours.

## SEO URL plugins

A build from the storefront, which is where every automatic rebuild happens,
makes links with `zen_href_link()`, so an SEO URL plugin shapes them as it
shapes every other link. A build triggered from the admin (Regenerate, or the
install) uses plain `index.php?main_page=` addresses, because the admin's own
link function builds admin addresses. Both forms resolve; the next storefront
rebuild replaces the plain ones.

## Translating

The words that appear in the generated file come from two language files, one
per side, because the file can be built from either:

```
catalog/includes/languages/english/extra_definitions/lang.llms_txt.php
admin/includes/languages/english/extra_definitions/lang.llms_txt_names.php
```

Copy each into the matching directory for your language inside the plugin and
translate the values. The admin page's own strings are in
`admin/includes/languages/english/lang.llms_txt.php`.
