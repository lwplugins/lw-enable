<?php
/**
 * Enable Service for LW Site Manager abilities.
 *
 * @package LightweightPlugins\Enable
 */

declare(strict_types=1);

namespace LightweightPlugins\Enable\SiteManager;

use LightweightPlugins\Enable\Options;

/**
 * Executes Enable abilities for the Site Manager.
 */
final class EnableService {

	/**
	 * Known feature flag keys.
	 */
	private const FEATURE_KEYS = array( 'svg' );

	/**
	 * Get all LW Enable options.
	 *
	 * @param array<string, mixed> $input Input parameters (unused).
	 * @return array<string, mixed>
	 */
	public static function get_options( array $input ): array { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found -- Required by ability callback interface.
		return array(
			'success' => true,
			'options' => Options::get_all(),
		);
	}

	/**
	 * Set LW Enable options.
	 *
	 * @param array<string, mixed> $input Input parameters.
	 * @return array<string, mixed>|\WP_Error
	 */
	public static function set_options( array $input ): array|\WP_Error {
		$incoming = $input['options'] ?? array();

		if ( ! is_array( $incoming ) || empty( $incoming ) ) {
			return new \WP_Error(
				'invalid_options',
				__( 'Provide an options object with at least one feature key.', 'lw-enable' ),
				array( 'status' => 400 )
			);
		}

		$current = Options::get_all();
		$updated = array();

		foreach ( $incoming as $key => $value ) {
			if ( in_array( $key, self::FEATURE_KEYS, true ) ) {
				$current[ $key ] = (bool) $value;
				$updated[]       = $key;
			}
		}

		if ( empty( $updated ) ) {
			return new \WP_Error(
				'no_valid_keys',
				sprintf(
					/* translators: %s: comma-separated list of valid keys */
					__( 'No valid feature keys provided. Valid keys: %s.', 'lw-enable' ),
					implode( ', ', self::FEATURE_KEYS )
				),
				array( 'status' => 400 )
			);
		}

		Options::save( $current );

		return array(
			'success' => true,
			'message' => sprintf(
				/* translators: %d: number of options updated */
				__( '%d option(s) updated.', 'lw-enable' ),
				count( $updated )
			),
			'options' => Options::get_all(),
		);
	}
}
