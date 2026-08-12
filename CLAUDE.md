# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

PHP client library (`atk14/pupiq-client`) for [Pupiq](https://i.pupiq.net/), a cloud image/attachment hosting service. It builds and parses Pupiq image/attachment URLs, generates signed transformation URLs (resize, crop, format conversion, watermark), and talks to the Pupiq HTTP API for uploads and metadata. Designed to plug into ATK14 Framework apps (form fields, widgets, Smarty template modifiers) but the core `Pupiq` class has no ATK14 dependency and works standalone.

Supports PHP 5.6 through 8.5 (see the test matrix in `.github/workflows/tests.yml`) — avoid syntax/features newer than PHP 5.6 in `src/lib/`.

## Commands

Install dependencies:

    composer update

Run the full test suite (must `cd test` first):

    cd test && ../vendor/bin/run_unit_tests

Run a specific test case (name without the `.php` extension, without `tc_` also works per the class name):

    cd test && ../vendor/bin/run_unit_tests tc_pupiq

Run multiple specific test cases:

    cd test && ../vendor/bin/run_unit_tests tc_pupiq tc_pupiq_utils

`run_unit_tests` is provided by `atk14/tester` (a PHPUnit-version-agnostic wrapper) and exits `0`/`1` for CI use. `test/tc_linter.php` runs `php -l` over every `.php` file outside `test/` and `vendor/` — keep new files free of syntax errors for that PHP version.

Test entrypoint `test/initialize.php` defines `PUPIQ_API_KEY` to a demo key and requires the lib files directly (no autoloading in tests) before instantiating a fake `$HTTP_REQUEST`/`$HTTP_RESPONSE`.

## Architecture

### Core class: `src/lib/pupiq.php` (`Pupiq`)

Everything centers on parsing/rebuilding a single URL format:

    http://i.pupiq.net/i/<user_id_hex>/<image_id_hex>/[w/<watermark>/<rev>/]<orig_w>x<orig_h>/<code>_<w>x<h>[xborder]_<token>.<suffix>

`setUrl()` regex-parses this into original dimensions, user/image IDs, code, watermark, and suffix; `getUrl()` rebuilds it (optionally after `setTransformation()`/`setGeometry()` changes the requested width/height/crop/format). The `<token>` segment is an HMAC-like value computed by `_calcToken()` from the transformation string, watermark, code, and API key — the Pupiq server re-derives and validates it, so **the token must exactly match what the server expects**; be careful with any change to `_calcToken()`, `getTransformation()`, or the transformation-string format.

Transformation-string mini-language handled by `setTransformation()` (see README "Usage in templates" for the full grammar): plain dimensions (`80`, `x60`, `80x80`), crop (`!80x80` / `80x80xcrop[,top|,bottom]`), background fill (`80x80x#ffffff`, `80x80xtransparent`, `80x80xtransparent_or_#ffffff`), format override (`,format=webp`), enlargement (`,enable_enlargement`), and watermark (`,watermark` / `,watermark=name`). Trailing options after the first comma are decoded by `PupiqUtils::DecodeParams()`.

Enlargement is disabled by default for raster formats (never upscale beyond the original) but enabled by default for `svg`.

Two auth mechanisms, both derived from the API key (`101.DemoApiKeyForAccountWithLimitedFunctions` format — `<user_id>.<secret>`):
- `_calcToken()` — per-URL signature embedded in generated image URLs, validated by the Pupiq CDN/server.
- `getAuthToken()` / `getAllowedAuthTokens()` — rotates every 10 minutes (`time() - (time() % 600)`), used to authenticate API calls (upload, color detection, original download). `getAllowedAuthTokens()` returns the current token plus the ones from ±5 minutes to tolerate clock skew across requests.

Static factory methods `Pupiq::CreateImage()` / `Pupiq::CreateAttachment()` upload via `ApiDataFetcher` (from `atk14/api-data-fetcher`) to the `images/create_new` / `attachments/create_new` API endpoints and construct a `Pupiq`/`PupiqAttachment` from the returned URL. `Pupiq::ToObject()` just wraps an already-known URL without an API call.

All `PUPIQ_*` configuration constants are defined with `defined(...) || define(...)` guards at the top of `pupiq.php`, so consuming apps set them in `config/settings.php` (or similar) *before* this file is first loaded.

### `src/lib/pupiq_attachment.php` (`PupiqAttachment`)

Lighter counterpart for non-image files (`/a/...` URLs vs. `/i/...`). No transformation logic — just filename/suffix/filesize/mime-type extraction via regex on the URL, since attachment URLs embed the filesize and filename directly.

### `src/lib/pupiq_error_handler.php` (`PupiqErrorHandler`)

Implements the optional "local proxy" feature described in the README: when a `/i/...` or `/a/...` URL 404s locally, `HandleRequest()` fetches it from the real Pupiq host via `UrlFetcher` (from `atk14/url-fetcher`), writes it to disk under `ATK14_DOCUMENT_ROOT` (atomically, via a uniquely-named temp file that gets renamed), and streams the response back. This is wired up via `.htaccess` `ErrorDocument 404` — see README "Set up local proxy".

### `src/lib/pupiq_utils.php` (`PupiqUtils`)

`DecodeParams()` parses comma-separated `key=value,flag` strings (with `\,`/`\=` escaping and quote-stripping) into an array — used both for transformation options and for HTML attribute strings passed to template modifiers.

### `src/app/` — ATK14 integration layer

Not autoloaded by Composer (only `src/lib` is in `composer.json`'s classmap); consuming apps symlink individual files in, per the README's Installation section:
- `fields/` — `PupiqImageField`, `PupiqAttachmentField`, `AsyncPupiqAttachmentField` (ATK14 form fields; `clean()` uploads the file via `Pupiq::CreateImage`/`CreateAttachment` and returns a `Pupiq`/`PupiqAttachment` instance as the cleaned value).
- `widgets/` — corresponding `*Input` widgets that render the upload UI (preview image, remove checkbox, drag-drop for async variants).
- `helpers/` — Smarty template modifiers (`pupiq_img`, `img_url`, `img_attrs`, `img_width`, `img_height`, `img_color`, `img_format`) that are thin wrappers translating template syntax into `Pupiq` method calls.

### `src/i/`, `src/a/` — local proxy scaffolding

Symlink targets (`.htaccess`, `error.php`) for the optional local-proxy setup; `error.php` in each just delegates to `PupiqErrorHandler::HandleRequest()`.

## Conventions to preserve

- The library still targets PHP 5.6 syntax in `src/lib/` (no typed properties, no arrow functions, etc.) — the CI matrix tests down to 5.6.
- Some public method names have Czech-influenced typos preserved for backwards compatibility (e.g. `getAscpectRation()`) — do not "fix" these without a deprecation path, since they're part of the public API.
- `CHANGELOG.md` is maintained by hand per release with commit hashes; update it when making a user-facing change intended for release, following the existing `## [x.y.z] - YYYY-MM-DD` format.
- Regexes encoding the URL format (in `setUrl()`, `getAttachmentId()`, etc.) are the source of truth for the wire format shared with the Pupiq server — changing them without a corresponding server-side change will break URL generation/parsing.
