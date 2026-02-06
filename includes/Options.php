<?php
/**
 * Options handler.
 *
 * @package LightweightPlugins\Enable
 */

declare(strict_types=1);

namespace LightweightPlugins\Enable;

/**
 * Manages plugin options.
 */
final class Options {

	/**
	 * Option name.
	 */
	private const OPTION_NAME = 'lw_enable';

	/**
	 * Cached options.
	 *
	 * @var array|null
	 */
	private static ?array $options = null;

	/**
	 * Get default options.
	 *
	 * @return array<string, bool>
	 */
	public static function get_defaults(): array {
		return array(
			'svg' => false,
		);
	}

	/**
	 * Get all options.
	 *
	 * @return array<string, bool>
	 */
	public static function get_all(): array {
		if ( null === self::$options ) {
			$saved         = get_option( self::OPTION_NAME, array() );
			self::$options = wp_parse_args( $saved, self::get_defaults() );
		}

		return self::$options;
	}

	/**
	 * Get single option.
	 *
	 * @param string $key Option key.
	 * @return bool
	 */
	public static function get( string $key ): bool {
		$options = self::get_all();
		return ! empty( $options[ $key ] );
	}

	/**
	 * Save options.
	 *
	 * @param array $options Options to save.
	 * @return void
	 */
	public static function save( array $options ): void {
		update_option( self::OPTION_NAME, $options );
		self::$options = null;
	}
}
