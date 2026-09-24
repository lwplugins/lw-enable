<?php
/**
 * Settings sanitizer.
 *
 * @package LightweightPlugins\Enable
 */

declare(strict_types=1);

namespace LightweightPlugins\Enable\Admin;

/**
 * Sanitizes submitted settings against the option defaults.
 *
 * Every LW Enable option is an on/off switch, so each submitted value is
 * cast to a boolean ("false", "0", "" and false all mean off). A value that
 * cannot be read as a switch (an array or object) keeps the current value.
 */
final class SettingsSanitizer {

	/**
	 * Sanitize the submitted keys.
	 *
	 * @param array<string, mixed> $submitted Submitted values.
	 * @param array<string, mixed> $current   Current values of every key.
	 * @param array<string, mixed> $defaults  Option defaults.
	 * @return array<string, bool> Sanitized values for the submitted known keys.
	 */
	public static function sanitize( array $submitted, array $current, array $defaults ): array {
		$sanitized = array();

		foreach ( $submitted as $key => $value ) {
			if ( ! array_key_exists( $key, $defaults ) ) {
				continue;
			}

			$fallback                   = (bool) ( $current[ $key ] ?? $defaults[ $key ] );
			$sanitized[ (string) $key ] = is_scalar( $value ) || null === $value
				? filter_var( $value, FILTER_VALIDATE_BOOLEAN )
				: $fallback;
		}

		return $sanitized;
	}
}
