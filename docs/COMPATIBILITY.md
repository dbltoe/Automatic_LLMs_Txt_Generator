# Compatibility notes: Zen Cart v1.5.8 → v3.0.0, PHP 7.4 → 8.5

This plugin runs unmodified across six Zen Cart release branches. That comes
from choosing, at each decision point, the mechanism that exists in *every*
supported version rather than the newest one. This document records those
choices so a future maintainer knows which ones are load-bearing.

## How it is verified

Not by assertion. The harnesses under `tests/` (which don't ship) run against
the real thing:

| Check | Method |
|---|---|
| **Zen Cart API** | Every core function, notifier and constant the plugin uses is looked up in the source of all six release branches, installed under `F:\zclab\sites`. A symbol that exists on `master` but not `v158` fails the build. |
| **PHP** | Lint plus the whole suite on 7.4, 8.0, 8.1, 8.2, 8.3, 8.4 and 8.5, run with `-n` (no php.ini, so no extensions) and `E_ALL`. Zero diagnostics. |
| **Behavior** | Installed through Plugin Manager on all six branches in the lab; the live page, the file at the root, the admin page and its three actions exercised on each. |

---

## What we rely on

### A plugin page directory: `catalog/includes/modules/pages/llms_txt/`

`PageLoader::findModulePageDirectory()` looks in the store's own
`includes/modules/pages/` first and then in every installed plugin's
`catalog/includes/modules/pages/` on **every** release from v1.5.8. So
`index.php?main_page=llms_txt` reaches this plugin's `header_php.php` with
no file copied into the store. The page sends the text and calls `exit`
before the template renders, the way sitemap plugins do.

### Observer registration: `auto.*.php` + `zcObserver*`

`includes/init_includes/init_observers.php` is run for the admin as well as
the storefront, and loads `auto.*.php` files from
`zc_plugins/<plugin>/<version>/<context>/includes/classes/observers/` on every
release, instantiating `'zcObserver' . base::camelize($name, true)`. The
admin observer therefore lives at
`admin/includes/classes/observers/auto.llms_txt.php` as `zcObserverLlmsTxt`.

The observer's `update()` is declared with the five arguments every release
passes (`&$class, $eventID, $param1, &$param2, &$param3`), all optional. No
release's `base` class declares an `update()` of its own, so there is no
signature to conflict with.

### Five admin notifiers, all present v1.5.8 → v3.0.0-dev

| Notifier | Fires on |
|---|---|
| `NOTIFY_MODULES_UPDATE_PRODUCT_END` | product save |
| `NOTIFY_ADMIN_CATEGORIES_UPDATE_OR_INSERT_FINISH` | category save |
| `NOTIFY_ADMIN_EZPAGES_UPDATE_BASE` | EZ-Page save |
| `NOTIFY_ADMIN_EZPAGES_UPDATE_LANG_INSERT` | EZ-Page text insert |
| `NOTIFY_ADMIN_EZPAGES_UPDATE_LANG_UPDATE` | EZ-Page text update |

`NOTIFY_ADMIN_PRODUCT_PRICE_EDIT_ABOVE` and friends are v2.1.0+ and are not
used. Whatever an observer misses (a delete, a status flip from a listing) is
caught by the catalog fingerprint the storefront compares.

### Autoload breakpoint 178

The storefront init script runs at 178: after `init_observers.php` (175) and
before `init_header.php` (180). The database, the configuration constants and
the language files are in place, nothing has produced output, and
`$_GET['main_page']` has been sanitized by `init_sanitize.php` (150), which is
what lets the script skip itself on the `llms_txt` page.

### `extra_datafiles` on both sides for `FILENAME_LLMS_TXT`

v2.2.0 and later also read a root-level `filenames.php`; v1.5.8 through
v2.1.0 read nothing of the kind, and a `FILENAME_*` constant that is never
defined breaks the admin menu entry silently. `admin/includes/extra_datafiles/`
and `catalog/includes/extra_datafiles/` are read by every release, so the
constant lives there, guarded with `defined()`.

### Array language files

`lang.*.php` returning an array is understood from v1.5.8, and v3.0.0 has
dropped the legacy `define()` loaders entirely. Every language file in this
plugin is the array form. The words that appear in the generated file are
defined on both sides (`catalog/.../extra_definitions/lang.llms_txt.php` and
`admin/.../extra_definitions/lang.llms_txt_names.php`) because the file can be
built from either, and the generator falls back to the same English when a key
is missing.

### `admin/includes/css/<page>.css`

`admin/includes/admin_html_head.php` links a plugin's
`admin/includes/css/<page basename>.css` on every release. The admin page's
stylesheet is `llms_txt.css`, and every rule in it is scoped to
`.llmstxt-page`.

### `DIR_FS_SQL_CACHE`

Defined by `includes/defined_paths.php` on the storefront and, on v3.0.0, by
the admin's own copy; the generator falls back to `DIR_FS_CATALOG . 'cache'`,
which is the value the core would have used. The state file and the rendered
cache live there because Zen Cart already requires it to be writable, so the
plugin works on a store whose root is not.

### `pluginDescription` renders as raw HTML in Plugin Manager

The Read Me and GitHub buttons in the info panel come from `manifest.php`.
On v1.5.8, v2.0 and v2.1 that description is written by the INSERT that
creates the `plugin_control` row and never refreshed, so nothing
state-dependent is placed there. The same applies to the forum thread URL:
it must be set before the first release.

---

## What we deliberately avoid

### `admin/includes/functions/extra_functions/`

Loaded by `admin/includes/application_bootstrap.php` from v1.5.8 through
v2.3 -- and **removed from master on 2026-06-08** (zencart/zencart commit
489b406, "Don't load plugins' extra_functions in application_bootstrap.php",
for issue #7788). A plugin function file there is silently ignored on
v3.0.0-dev. No release ever auto-loaded a plugin's
`catalog/includes/functions/extra_functions/` either.

So this plugin has no function files in either place. Everything lives in
`shared/generator.php`, and every entry point (`header_php.php`, the init
script, the observer, the admin page, the installer) `require_once`s it by
its own `__DIR__` path.

### PSR-4 plugin classes

Registered at different paths on v1.5.8 and v3.0.0. Plain prefixed functions
and one non-namespaced observer class instead.

### The `ScriptedInstaller` convenience helpers

`addConfigurationKey()`, `getOrCreateConfigGroupId()` and the rest arrived in
v2.0.1/v2.1.0. The installer uses only `executeInstallerSql()` and
`$this->dbConn`, present since v1.5.7, and declares
`executeUpgrade($oldVersion = null)` because v1.5.8 calls it with no argument.

### `zen_config()`

v3.0.0 only. The generator's `llmstxt_cfg()` is the portable form:
`defined($key) ? constant($key) : $default`.

### `zen_href_link()` in the admin

Builds admin addresses. Storefront links from an admin-triggered build are
assembled by hand from `HTTP_CATALOG_SERVER` / `HTTPS_CATALOG_SERVER` and
`DIR_WS_CATALOG`. On the storefront the real function is used so an SEO-URL
plugin shapes the links -- and its `&amp;` separator, correct for an HTML
attribute, is decoded back to `&` because the output is a text file. The lab
caught that one: the storefront-built file was 736 bytes larger than the
admin-built one.

### `status_mobile` on `ezpages`

Absent on v1.5.8. The EZ-Pages query never names it.

---

## PHP

The source must parse on 7.4 and stay deprecation-clean on 8.5. In practice:

- No `match`, nullsafe `?->`, named arguments, union types, constructor
  promotion, `readonly`, enums, `never`, first-class callables,
  `str_contains()` and friends, or `mixed`.
- Every parameter that may be null is declared `?T` or left untyped;
  `f(T $x = null)` is deprecated from 8.4.
- No dynamic properties; the observer declares none.
- `mb_*` functions are used when present and fall back to byte functions,
  so the plugin runs without `ext/mbstring`. The suite proves it by running
  with `-n`.
- Every file closes with `Throwable` catches around anything that runs on a
  storefront or admin page that is not this plugin's own, so a failure here
  can never take a store down.

## If you are adding to this plugin

Before using any Zen Cart function, constant or notifier, check it exists on
`v158` as well as `master`. `tests/zc_compat.php` does this automatically
against the lab; without the lab, fetch the branches once and grep:

```bash
git clone --depth 1 --branch master https://github.com/zencart/zencart.git zc
git -C zc remote set-branches --add origin 2.0 2.1 2.2 2.3 v158
git -C zc fetch --depth 1 origin 2.0 2.1 2.2 2.3 v158
git -C zc grep -c "function zen_whatever" origin/v158 origin/master
```
