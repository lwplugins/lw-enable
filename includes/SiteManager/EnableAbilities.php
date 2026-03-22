<?php
/**
 * Enable Ability Definitions for LW Site Manager.
 *
 * @package LightweightPlugins\Enable
 */

declare(strict_types=1);

namespace LightweightPlugins\Enable\SiteManager;

/**
 * Registers Enable-specific abilities with the WordPress Abilities API.
 */
final class EnableAbilities {

	/**
	 * Register all Enable abilities.
	 *
	 * @param object $permissions Permission manager instance.
	 * @return void
	 */
	public static function register( object $permissions ): void {
		self::register_get_options( $permissions );
		self::register_set_options( $permissions );
	}

	/**
	 * Register the get-options ability.
	 *
	 * @param object $permissions Permission manager instance.
	 * @return void
	 */
	private static function register_get_options( object $permissions ): void {
		wp_register_ability(
			'lw-enable/get-options',
			array(
				'label'               => __( 'Get Enable Options', 'lw-enable' ),
				'description'         => __( 'Get all LW Enable feature toggle settings.', 'lw-enable' ),
				'category'            => 'enable',
				'execute_callback'    => array( EnableService::class, 'get_options' ),
				'permission_callback' => $permissions->callback( 'can_manage_options' ),
				'input_schema'        => array(
					'type'    => 'object',
					'default' => array(),
				),
				'output_schema'       => array(
					'type'       => 'object',
					'properties' => array(
						'success' => array( 'type' => 'boolean' ),
						'options' => array( 'type' => 'object' ),
					),
				),
				'meta'                => self::readonly_meta(),
			)
		);
	}

	/**
	 * Register the set-options ability.
	 *
	 * @param object $permissions Permission manager instance.
	 * @return void
	 */
	private static function register_set_options( object $permissions ): void {
		wp_register_ability(
			'lw-enable/set-options',
			array(
				'label'               => __( 'Set Enable Options', 'lw-enable' ),
				'description'         => __( 'Toggle LW Enable features on or off.', 'lw-enable' ),
				'category'            => 'enable',
				'execute_callback'    => array( EnableService::class, 'set_options' ),
				'permission_callback' => $permissions->callback( 'can_manage_options' ),
				'input_schema'        => array(
					'type'       => 'object',
					'required'   => array( 'options' ),
					'properties' => array(
						'options' => array(
							'type'        => 'object',
							'description' => __( 'Feature flags to update. Keys: svg (bool).', 'lw-enable' ),
						),
					),
				),
				'output_schema'       => array(
					'type'       => 'object',
					'properties' => array(
						'success' => array( 'type' => 'boolean' ),
						'message' => array( 'type' => 'string' ),
						'options' => array( 'type' => 'object' ),
					),
				),
				'meta'                => self::write_meta(),
			)
		);
	}

	/**
	 * Read-only ability metadata.
	 *
	 * @return array<string, mixed>
	 */
	private static function readonly_meta(): array {
		return array(
			'show_in_rest' => true,
			'annotations'  => array(
				'readonly'    => true,
				'destructive' => false,
				'idempotent'  => true,
			),
		);
	}

	/**
	 * Write ability metadata.
	 *
	 * @return array<string, mixed>
	 */
	private static function write_meta(): array {
		return array(
			'show_in_rest' => true,
			'annotations'  => array(
				'readonly'    => false,
				'destructive' => false,
				'idempotent'  => true,
			),
		);
	}
}
