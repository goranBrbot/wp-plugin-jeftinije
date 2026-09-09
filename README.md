# wp-plugin-jeftinije

Fork of the **Shopper's Mind** WooCommerce plugin, updated for PHP 8.1+ and current WordPress. Original plugin by Shopper's Mind (smind.si) exports WooCommerce products as an XML feed for the comparison-shopping platforms ceneje.si, jeftinije.hr, idealno.rs and idealno.ba.

## Why this fork exists

An older custom `page-jeftinije.php` export script was audited for compatibility with current WordPress/PHP. That audit surfaced two structural problems that were hard to fix cheaply:

1. The script relied on a `page-{slug}.php` template, which block themes can ignore entirely — the feed can silently stop working on theme switch.
2. Reading WooCommerce product attributes mixed array- and object-style access, which throws a fatal error on taxonomy-based attributes.

Shopper's Mind already solves both: it registers a WordPress **REST API** route (theme-independent) and consistently uses `get_terms()`/`get_options()` for attributes. Rather than rewrite a plugin from scratch, this fork takes Shopper's Mind's source (last released ~2021, PHP 5.2+) and brings it up to date instead.

## Changes made in this fork

Verified with `phpcs` against the `PHPCompatibilityWP` standard, `testVersion 8.1-` (zero findings) plus manual review:

- **Fixed broken CDATA tag** — [`src/Export/WooCommerceExport.php`](src/Export/WooCommerceExport.php): the `pluginVersion` node was missing its opening `<![CDATA[`, so the tag content was emitted as literal text instead of a CDATA section, producing invalid-looking output.
- **Added `permission_callback` to the REST export route** — [`src/Export/XMLEndpoint.php`](src/Export/XMLEndpoint.php): missing on WP 5.5+ triggers a `_doing_it_wrong` notice. Set to `__return_true` since the feed is intentionally public (comparison-shopping platforms fetch it unauthenticated).
- **Guarded against null/false returns from WooCommerce APIs** in [`src/Export/WooCommerceExport.php`](src/Export/WooCommerceExport.php), any of which could previously throw a fatal error and abort the whole export mid-run:
  - `wc_get_product()` for a variation ID that no longer resolves to a product.
  - `get_term_by()` when a variation's stored attribute value doesn't match a term.
  - `wc_get_attribute()` when a configured attribute ID no longer exists.
  - A product with no assigned category (indexing an empty array).
- **Raised `Requires PHP` to 8.1** in the plugin header and `readme.txt`.
- **Fixed the `pluginVersion` value reported in the feed** — [`src/Config/Config.php`](src/Config/Config.php): was hardcoded to `1.0.1`, one behind the actual plugin version; caught while validating the live feed output.
- **Renamed the plugin and added an `Update URI` header** — [`shoppers_mind.php`](shoppers_mind.php): WordPress derives the "View plugin details" slug from the `Plugin Name` header, and since this fork kept the original name (`Shopper's Mind`), that slug collided with the original plugin's real listing on wordpress.org. WordPress admin was showing *that* plugin's version, changelog, "tested up to" and contributors instead of this fork's — and would have offered an "update" that silently overwrites this fork with the unfixed original. Renaming to `Shopper's Mind (Fork by Goran Brbot)` breaks the collision; the added `Update URI` header is WordPress's own mechanism (since 5.8) for telling core not to check wordpress.org for updates on a fork.

## Full audit pass (1.0.4)

A follow-up pass reviewed the remaining parts of the plugin not touched by the initial fork work (admin settings, widget, script enqueueing, plugin header) for 2026-era WordPress/WooCommerce standards:

- **Fixed a fatal error on every wp-admin page when WooCommerce is deactivated** — [`shoppers_mind.php`](shoppers_mind.php): the settings-page bootstrap in [`src/Admin/AdminPluginForm.php`](src/Admin/AdminPluginForm.php) ran on the global `admin_init` hook and called `get_woocommerce_currency_symbol()` unconditionally, with no `class_exists('WooCommerce')` guard anywhere in the codebase. The entire plugin bootstrap now short-circuits (showing an admin notice instead) when WooCommerce isn't active.
- **Added a `Requires Plugins: woocommerce` header** (the WP 6.5+ mechanism for declaring plugin dependencies) so WordPress blocks activation without WooCommerce, and also guarantees WooCommerce loads first so the runtime `class_exists` check above is reliable.
- **Declared WooCommerce HPOS (custom order tables) compatibility** on `before_woocommerce_init` — the plugin never touches order data, but WooCommerce shows an incompatibility notice for any plugin lacking the explicit declaration.
- **Fixed a `wp_enqueue_script()` misuse** — [`src/Plugin.php`](src/Plugin.php): the second parameter is `$src`, not `$deps`; `wp_enqueue_script($scriptName, array('jquery'))` silently corrupted the registered script's source and never actually declared the jQuery dependency.
- **Escaped output that was missing it** — the widget title in [`src/Widgets/CsTrustmarkWidget.php`](src/Widgets/CsTrustmarkWidget.php) (`esc_html`) and an admin help link in `AdminPluginForm.php` (`esc_url`), for consistency with the `esc_url()`/`esc_attr()` pattern already used elsewhere on that page.
- **Used a real capability instead of a role name** for the settings page (`manage_options` instead of `'administrator'`), per the WordPress Plugin Handbook's capability-check guidance.
- **Tightened sanitization of the XML feed URL setting** to a safe REST-route character set, since it's passed directly as a `register_rest_route()` path segment.
- **Fixed three sanitize callbacks that wiped a setting to empty on invalid input** instead of keeping the previously stored value.
- **Renamed the widget class to `CsTrustmarkWidget`** to match its filename (it was `TrustmarkWidget`), for consistency now that the codebase doesn't use PSR-4 autoloading.

Intentionally left as-is: i18n (no `Text Domain`/`__()` wrapping — this is an internal single-site fork, not slated for wordpress.org) and switching the manual `require_once` chain to Composer's PSR-4 autoloading (no functional benefit for a plugin this size).

## Building an installable zip

```powershell
powershell -File scripts\build-zip.ps1
```

Writes the current runtime files (no `.git`, `vendor/`, `composer.*`, or this README) to `dist/wp-plugin-jeftinije.zip`, ready to upload via Plugins → Add New → Upload Plugin. Re-run it after any code change to keep `dist/` at the latest version; the zip itself is git-ignored, so it's always a local build, not a stale committed copy.

## Development

Dev tooling (`squizlabs/php_codesniffer` + `phpcompatibility/phpcompatibility-wp`) is declared in `composer.json` as dev dependencies only — not required at plugin runtime, and `vendor/` is git-ignored.

```bash
composer install
vendor/bin/phpcs --standard=PHPCompatibilityWP --runtime-set testVersion 8.1- shoppers_mind.php src/
```

## License

GPLv2 or later, same as upstream. See [LICENSE.txt](LICENSE.txt). Copyright 2016–2021 Shopper's Mind; fork changes as noted above.
