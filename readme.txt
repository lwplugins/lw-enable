=== LW Enable ===
Contributors: lwplugins
Tags: enable, svg, upload, media
Requires at least: 6.0
Tested up to: 6.7
Stable tag: 1.0.12
Requires PHP: 8.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Enable WordPress features: SVG uploads and more.

== Description ==

Lightweight plugin to enable extra WordPress features safely.

= Media =

* **SVG Uploads** - Allow SVG file uploads with comprehensive security sanitization
  * MIME type registration
  * Script/XSS detection and blocking
  * XXE (XML External Entity) protection
  * Event handler detection
  * Obfuscation detection (Unicode/hex escapes, entity encoding)
  * Automatic SVG dimension extraction (width/height and viewBox)
  * 5MB file size limit
  * Post-upload re-validation

Part of [LW Plugins](https://lwplugins.com) - lightweight WordPress plugins.

== Installation ==

1. Upload to `/wp-content/plugins/lw-enable/`
2. Activate the plugin
3. Go to LW Plugins → Enable
4. Enable SVG Uploads

Or: `composer require lwplugins/lw-enable`

== WP-CLI ==

Manage features via command line.

= List all features =

`wp lw-enable list`

Shows a table with all features and their current status (enabled/disabled).

= Enable a feature =

`wp lw-enable enable <feature>`

Example:
`wp lw-enable enable svg`

= Disable a feature =

`wp lw-enable disable <feature>`

Example:
`wp lw-enable disable svg`

= Enable all features =

`wp lw-enable enable-all`

Enables all features at once.

= Disable all features =

`wp lw-enable disable-all`

Disables all features (restores defaults).

= Available features =

* svg - SVG file uploads

== Frequently Asked Questions ==

= Is SVG upload safe? =

The plugin performs comprehensive sanitization: script detection, XSS prevention, XXE protection, event handler blocking, and obfuscation detection. Only clean SVGs pass validation.

= What is the maximum SVG file size? =

5MB per file.

== Changelog ==

= 1.0.12 =
* Update: Added PHPStan level 5 static analysis and a PHPUnit test suite (including SVG sanitizer security tests) to CI. No functional changes.

= 1.0.11 =
* New: LW Site Manager integration - enable abilities for AI agents
* New: lw-enable/get-options - get enabled features
* New: lw-enable/set-options - toggle features on/off

= 1.0.10 =
* Fix: Smarter autoloader fallback - supports root Composer dependency installs

= 1.0.9 =
* Fix: Graceful error when autoloader is missing (admin notice instead of fatal error)

= 1.0.8 =
* Minor fix

= 1.0.7 =
* Hash-based tab navigation on settings page
* Moved save handler to admin_init for proper redirect
* New circle-check icon
* Updated ParentPage with SVG icon support from registry

= 1.0.6 =
* Minor fix

= 1.0.5 =
* Minor fix

= 1.0.4 =
* Fix admin notice isolation for notices relocated by WordPress core JS

= 1.0.3 =
* Isolate third-party admin notices on LW plugin pages

= 1.0.2 =
* Add fresh POT file and Hungarian (hu_HU) translation

= 1.0.1 =
* New: WP-CLI support (list, enable, disable, enable-all, disable-all)

= 1.0.0 =
* Initial release
* SVG upload support with security sanitization
