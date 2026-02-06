# Lightweight Enable

Enable WordPress features: SVG uploads and more.

[![Packagist](https://img.shields.io/packagist/v/lwplugins/lw-enable.svg)](https://packagist.org/packages/lwplugins/lw-enable)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-blue.svg)](https://php.net)
[![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-blue.svg)](https://wordpress.org)
[![License](https://img.shields.io/badge/License-GPL--2.0-green.svg)](https://www.gnu.org/licenses/gpl-2.0.html)

## Features

### Media

- **SVG Uploads** - Allow SVG file uploads with comprehensive security sanitization
  - MIME type registration
  - Script/XSS detection and blocking
  - XXE (XML External Entity) protection
  - Event handler detection
  - Obfuscation detection (Unicode/hex escapes, entity encoding)
  - Automatic SVG dimension extraction (width/height and viewBox)
  - 5MB file size limit
  - Post-upload re-validation

## Installation

```bash
composer require lwplugins/lw-enable
```

Or download and upload to `/wp-content/plugins/`.

## Usage

### Admin UI

1. Go to **LW Plugins → Enable**
2. Check the features you want to enable
3. Save

### WP-CLI

```bash
# List all features and their status
wp lw-enable list

# Enable a feature
wp lw-enable enable svg

# Disable a feature
wp lw-enable disable svg

# Enable all features at once
wp lw-enable enable-all

# Disable all features (restore defaults)
wp lw-enable disable-all
```

**Available features:**

| Feature | Description |
|---------|-------------|
| `svg` | SVG file uploads with security sanitization |

## Security

SVG files can contain malicious code. This plugin performs comprehensive sanitization before allowing uploads:

- **34 dangerous patterns** detected (scripts, event handlers, external entities, data URIs, etc.)
- **Entity decoding** to catch obfuscated payloads
- **XML validation** via DOMDocument
- **Post-upload verification** - files are re-checked after WordPress processes them
- **Automatic cleanup** - invalid files are deleted immediately

## Links

- [GitHub](https://github.com/lwplugins/lw-enable)
- [Packagist](https://packagist.org/packages/lwplugins/lw-enable)
- [LW Plugins](https://lwplugins.com)
