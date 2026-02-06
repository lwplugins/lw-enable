<?php
/**
 * Settings fields data.
 *
 * @package LightweightPlugins\Enable
 */

declare(strict_types=1);

namespace LightweightPlugins\Enable\Admin\Settings;

/**
 * Contains settings field definitions.
 */
final class FieldsData {

	/**
	 * Get sections configuration.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public static function get_sections(): array {
		return array(
			'media' => array(
				'title'  => __( 'Media', 'lw-enable' ),
				'icon'   => 'dashicons-admin-media',
				'fields' => array(
					'svg' => __( 'SVG Uploads', 'lw-enable' ),
				),
			),
		);
	}

	/**
	 * Get field descriptions.
	 *
	 * @return array<string, string>
	 */
	public static function get_descriptions(): array {
		return array(
			'svg' => __( 'Allow SVG file uploads with security sanitization', 'lw-enable' ),
		);
	}
}
