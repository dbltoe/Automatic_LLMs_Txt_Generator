# Changelog

All notable changes to this project are recorded here. This project follows
[Semantic Versioning](https://semver.org/).

## [1.0.1] — 2026-09-11

### Fixed

- Plugin Manager on Zen Cart 2.x with PHP 8 could fatal on this plugin's row,
  because the build carried a plugin id the zen-cart.com version server did not
  know. The id is now the one the Plugins Library assigned.
- A store with no name yet gets its host name as the heading instead of a
  blank line.

### Changed

- Renamed to Automatic LLMs.txt Generator: menu entries, configuration group
  and file marker. Settings carry over; the configuration group is renamed in
  place on upgrade.

### Added

- Brands section with product counts, a Robots line when `robots.txt` exists,
  and Markdown escaping of names and descriptions.
- Four notifier events (`NOTIFY_LLMSTXT_COLLECT_END`, `_RENDER_END`, `_SERVE`,
  `_PUBLISHED`) for companion plugins; the Pro edition requires this release.

## [1.0.0] — 2026-09-07

First release.

### Added

- Builds an `llms.txt` ([llmstxt.org](https://llmstxt.org/)) from the store:
  name and summary, optional notes, the shopping-information pages, the
  category tree, brands with product counts, products with prices, and
  published EZ-Pages. Links the sitemap and `robots.txt` when the store has
  them. Optionally an `llms-full.txt` with longer descriptions.
- Names and descriptions are Markdown-escaped, so a product called
  "50% off [Sale]" reads as written instead of breaking the line.
- Two ways to publish, or both: a real file at the store root, or the live page
  `index.php?main_page=llms_txt` behind a one-line rewrite rule.
- Rebuilds when the catalog changes (an admin save flags it; the storefront
  also compares a catalog fingerprint every fifteen minutes), daily, weekly,
  monthly, or only from the Regenerate button.
- Never overwrites an `llms.txt` it did not write, unless told to.
- Admin page under Tools: status, preview, Regenerate, Remove, and the rewrite
  snippet.
- One codebase for Zen Cart v1.5.8 through v3.0.0 on PHP 7.4 through 8.5.

[1.0.1]: https://github.com/dbltoe/Automatic_LLMs_Txt_Generator/releases/tag/v1.0.1
[1.0.0]: https://github.com/dbltoe/Automatic_LLMs_Txt_Generator/releases/tag/v1.0.0
