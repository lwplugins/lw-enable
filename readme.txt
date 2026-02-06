=== LW Enable ===
Contributors: lwplugins
Tags: enable, svg, upload, media
Requires at least: 6.0
Tested up to: 6.7
Stable tag: 1.0.0
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

== Frequently Asked Questions ==

= Is SVG upload safe? =

The plugin performs comprehensive sanitization: script detection, XSS prevention, XXE protection, event handler blocking, and obfuscation detection. Only clean SVGs pass validation.

= What is the maximum SVG file size? =

5MB per file.

== Changelog ==

= 1.0.0 =
* Initial release
* SVG upload support with security sanitization
