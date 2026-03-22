# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

## [1.0.11] - 2026-03-22

### Added
- LW Site Manager integration - enable abilities for AI agents
- `lw-enable/get-options` ability - get enabled features
- `lw-enable/set-options` ability - toggle features on/off

## [1.0.10]

### Fixed
- Smarter autoloader fallback - supports root Composer dependency installs

## [1.0.9]

### Fixed
- Graceful error when autoloader is missing (admin notice instead of fatal error)

## [1.0.8]

### Fixed
- Minor fix

## [1.0.7]

### Added
- Hash-based tab navigation on settings page
- New circle-check icon
- Updated ParentPage with SVG icon support from registry

### Changed
- Moved save handler to `admin_init` for proper redirect

## [1.0.6]

### Fixed
- Minor fix

## [1.0.5]

### Fixed
- Minor fix

## [1.0.4]

### Fixed
- Admin notice isolation for notices relocated by WordPress core JS

## [1.0.3]

### Changed
- Isolate third-party admin notices on LW plugin pages

## [1.0.2]

### Added
- Fresh POT file and Hungarian (hu_HU) translation

## [1.0.1]

### Added
- WP-CLI support (`list`, `enable`, `disable`, `enable-all`, `disable-all`)

## [1.0.0]

### Added
- Initial release
- SVG upload support with security sanitization
