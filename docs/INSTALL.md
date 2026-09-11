# Installing Automatic LLMs.txt Generator

Applies to Zen Cart v1.5.8 and later, including v2.x and the v3.0.0 development
branch. PHP 7.4 through 8.5.

---

## 1. Copy the files

This repository contains one directory that belongs in your store:

```
zc_plugins/LlmsTxt/
```

Upload it so it lands at:

```
<your store root>/zc_plugins/LlmsTxt/v1.0.1/
```

That directory should contain `manifest.php`, `readme.html`, `changelog.txt`,
and the `Installer/`, `admin/`, `catalog/` and `shared/` sub-directories.

Nothing goes anywhere else. The plugin doesn't drop files into your `admin/`,
`includes/` or template directories, which is what makes it safe to remove
later.

> **If your store is itself a git checkout of Zen Cart:** `zc_plugins/.gitignore`
> uses a deny-all rule with an explicit allowlist, so add `!LlmsTxt/` and
> `!LlmsTxt/**` to it or git won't see the plugin. This has no effect on the
> plugin working.

## 2. Install it

1. Log in to your Zen Cart admin.
2. Go to **Modules → Plugin Manager**.
3. Find **Automatic LLMs.txt Generator** and click **Install**.

> Selecting the plugin shows an info panel on the right. Alongside the
> description and the **Install / Uninstall / Disable** buttons you'll find
> **Read Me** and **GitHub** buttons. Read Me opens the full documentation, and
> both work whether or not the plugin is installed.

The installer creates a configuration group, adds **Automatic LLMs.txt Generator** to
the Tools menu, and writes a first `llms.txt` at the store root straight away
if it can. If the store root isn't writable it says so, and the Tools page
explains the alternative.

## 3. Write the summary

Open **Configuration → Automatic LLMs.txt Generator** and fill in **Store summary**: one
or two sentences on what you sell and who it's for. This is the first thing an
AI assistant reads, and it's the one part of the file that can't be generated
from your catalog.

## 4. Check it

Open **Tools → Automatic LLMs.txt Generator**. The status table says whether the file is
present, and the preview shows what's published. Then open
`https://your-store/llms.txt` in a browser.

That's a working install. Everything else is optional and described in
[CONFIGURATION.md](CONFIGURATION.md).

---

## Upgrading

Upload the new version directory beside the old one and press **Upgrade** in
Plugin Manager. Settings you've changed are kept; only labels, help text and
the defaults for new settings are refreshed. Then remove the old directory.

## Uninstalling

Plugin Manager's **Uninstall** removes the configuration group, the menu
entries and the cache. The generated `llms.txt` at the store root is left in
place on purpose: it's a public file that assistants may already know about,
and it keeps working without the plugin. Press **Remove the generated files**
on the Tools page first if you want it gone.
