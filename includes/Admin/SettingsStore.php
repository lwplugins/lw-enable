<?php
/**
 * Settings store for the admin API.
 *
 * @package LightweightPlugins\Enable
 */

declare(strict_types=1);

namespace LightweightPlugins\Enable\Admin;

use LightweightPlugins\Enable\Options;

/**
 * Reads the settings as booleans and applies partial updates.
 *
 * A partial update merges the submitted keys onto the stored options, so a
 * key the client did not send keeps its stored value (never "absent bool =
 * false"). Unknown keys are dropped. The write goes through Options::save(),
 * the same path the classic form, the CLI and the abilities use.
 */
final class SettingsStore {

	/**
	 * Current settings: every default key as a boolean.
	 *
	 * @return array<string, bool>
	 */
	public static function current(): array {
		Options::clear_cache();

		return self::typed( Options::get_all(), Options::get_defaults() );
	}

	/**
	 * Apply a partial update and return the new settings.
	 *
	 * @param array<string, mixed> $body Submitted option keys.
	 * @return array<string, bool>
	 */
	public static function save( array $body ): array {
		Options::clear_cache();

		Options::save( self::merge( $body, Options::get_all(), Options::get_defaults() ) );

		return self::current();
	}

	/**
	 * Merge submitted keys onto the stored options. The result holds exactly
	 * the default keys, like the classic save wrote.
	 *
	 * @param array<string, mixed> $body     Submitted option keys.
	 * @param mixed                $stored   Stored option value.
	 * @param array<string, mixed> $defaults Option defaults.
	 * @return array<string, bool>
	 */
	public static function merge( array $body, mixed $stored, array $defaults ): array {
		$stored  = is_array( $stored ) ? array_intersect_key( $stored, $defaults ) : array();
		$current = self::typed( array_merge( $defaults, $stored ), $defaults );

		return array_merge( $current, SettingsSanitizer::sanitize( $body, $current, $defaults ) );
	}

	/**
	 * Every default key cast to a boolean, in defaults order.
	 *
	 * @param array<string, mixed> $values   Option values.
	 * @param array<string, mixed> $defaults Option defaults.
	 * @return array<string, bool>
	 */
	public static function typed( array $values, array $defaults ): array {
		$typed = array();

		foreach ( $defaults as $key => $default ) {
			$typed[ $key ] = (bool) ( array_key_exists( $key, $values ) ? $values[ $key ] : $default );
		}

		return $typed;
	}
}
