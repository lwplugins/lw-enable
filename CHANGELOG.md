# Changelog

## [1.1.3] - 2026-09-26

### Changed
- The LW Plugins overview page is now a searchable table showing each LW plugin's status and version, with one-click activation for installed plugins; it always uses the newest version shipped by any active LW plugin.

### Fixed
- LW Site Manager's MCP server now lists this plugin's abilities (they were only reachable through REST).

## [1.1.2] - 2026-09-25

### Fixed
- `Requires at least` raised to WordPress 6.6: the React settings screen needs the `react-jsx-runtime` script that core registers from 6.6, so on older versions the page stayed blank without an error (verified on 6.5 and 6.6)

## [1.1.1] - 2026-09-25

### Fixed
- Notices from themes and other plugins (for example a theme's purchase-code or recommended-plugins notice) could show on the LW Enable screen. They are now kept off every LW Plugins screen, whatever their markup.
- The "settings screen files are missing" notice is no longer hidden by the notice isolation.

## [1.1.0] - 2026-09-24

### Added
- New settings screen built with WordPress components: side navigation, a top bar with Save/Discard and a Cmd/Ctrl+S shortcut, loading skeletons, a mobile layout and an Enabled/Disabled state next to each switch. Only the switches you changed are saved.
- Admin REST API under `lw-enable/v1/admin/settings` for users with `manage_options`.
- Hungarian translation of the new interface and of strings that were never translated (JavaScript translation file shipped in `languages/`).

### Changed
- The admin accent and the plugin logo use a darker green (#2e7d32) so white text on buttons is readable (5.1:1).
- The classic settings form, its stylesheet and script were removed.

## [1.0.14] - 2026-09-06

### Fixed
- The release package and the Composer/Packagist dist no longer ship tests, docs or development configuration (`.gitattributes` export-ignore plus unified release excludes). A hosting malware scanner had flagged a unit-test fixture on a customer site

## [1.0.13] - 2026-08-20

### Changed
- Tested up to WordPress 7.1.

## [1.0.12] - 2026-07-18

### Changed
- Added PHPStan level 5 static analysis and a PHPUnit test suite (including SVG sanitizer security tests) to CI. No functional changes.

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
