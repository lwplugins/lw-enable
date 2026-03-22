<?php
/**
 * LW Site Manager Integration.
 *
 * Registers Enable abilities when LW Site Manager is active.
 *
 * @package LightweightPlugins\Enable
 */

declare(strict_types=1);

namespace LightweightPlugins\Enable\SiteManager;

/**
 * Hooks into LW Site Manager to register Enable abilities.
 */
final class Integration {

	/**
	 * Initialize hooks. Safe to call even if Site Manager is not active.
	 *
	 * @return void
	 */
	public static function init(): void {
		add_action( 'lw_site_manager_register_categories', array( self::class, 'register_category' ) );
		add_action( 'lw_site_manager_register_abilities', array( self::class, 'register_abilities' ) );
	}

	/**
	 * Register the Enable ability category.
	 *
	 * @return void
	 */
	public static function register_category(): void {
		wp_register_ability_category(
			'enable',
			array(
				'label'       => __( 'Enable', 'lw-enable' ),
				'description' => __( 'WordPress feature toggle abilities', 'lw-enable' ),
			)
		);
	}

	/**
	 * Register Enable abilities.
	 *
	 * @param object $permissions Permission manager from Site Manager.
	 * @return void
	 */
	public static function register_abilities( object $permissions ): void {
		EnableAbilities::register( $permissions );
	}
}
