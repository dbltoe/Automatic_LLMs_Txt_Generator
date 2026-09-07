# LLMs.txt Generator

An encapsulated Zen Cart plugin that publishes an `llms.txt` for your store:
the short Markdown guide that AI assistants read to understand a website, the
way search engines read `sitemap.xml`. It is built from what is already in
Zen Cart and it keeps itself current.

**Runs on Zen Cart v1.5.8 through v3.0.0 and PHP 7.4 through 8.5 from a single
codebase**, verified against all six release branches rather than assumed. See
[docs/COMPATIBILITY.md](docs/COMPATIBILITY.md).

---

## What it produces

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

The format follows [llmstxt.org](https://llmstxt.org/): one H1, a blockquote
summary, optional free text, then H2 sections of `- [name](url): notes` lines.
Optionally a second `llms-full.txt` carries the same sections with complete
descriptions.

## How it publishes

| Setting | What happens |
|---|---|
| **file** (default) | A real `llms.txt` is written at the store root, next to `robots.txt`. No other setup. |
| **dynamic** | Served live by `index.php?main_page=llms_txt`, behind three lines of `.htaccess` so `/llms.txt` reaches it. Nothing written at the root. |
| **both** | The file is written and the live page stays as a fallback. |

## When it rebuilds

| Setting | Rebuilds when |
|---|---|
| **change** (default) | A product, category or EZ-Page is saved in the admin, and whenever a fifteen-minute storefront check finds the catalog fingerprint has moved. |
| **daily / weekly / monthly** | That long after the previous build, plus after an admin save. |
| **manual** | Only from the **Regenerate now** button. |

Zen Cart has no scheduler, so the storefront does the work on its next visit,
never the admin. An admin save only sets a flag, so saving a product stays as
fast as it was.

## What it will not do

- Overwrite an `llms.txt` it did not write. A hand-made file is recognized and
  left alone until you press **Replace** on the Tools page.
- Put files anywhere but `zc_plugins/LlmsTxt/`. Nothing lands in `admin/`,
  `includes/` or your template, which is what makes it safe to remove.
- Make a storefront page wait for a build. A crawler is always served the
  cached copy; the rebuild happens behind it.

## Installing

Upload `zc_plugins/LlmsTxt/` to your store root, then **Modules → Plugin
Manager → LLMs.txt Generator → Install**. Full steps in
[docs/INSTALL.md](docs/INSTALL.md); every setting in
[docs/CONFIGURATION.md](docs/CONFIGURATION.md); the shipped
[readme.html](readme.html) covers both and is linked from Plugin Manager.

## Requirements

- Zen Cart v1.5.8 or later, including v2.x and the v3.0.0 development branch.
- PHP 7.4 through 8.5.
- A writable `cache/` directory, which Zen Cart already requires.
- For **file** output, a store root writable by the web server. For
  **dynamic** output, the ability to edit `.htaccess`.

## License

GPL-2.0. See [LICENSE](LICENSE).
