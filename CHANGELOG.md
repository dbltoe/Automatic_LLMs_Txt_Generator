# Changelog

All notable changes to this project are recorded here. This project follows
[Semantic Versioning](https://semver.org/).

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

[1.0.0]: https://github.com/dbltoe/Automatic_LLMs_Txt_Generator/releases/tag/v1.0.0
