<?php
/**
 * Read-only structure of the settings screen.
 *
 * @package LightweightPlugins\Enable
 */

declare(strict_types=1);

namespace LightweightPlugins\Enable\Rest\Admin;

/**
 * Turns the FieldsData definitions into the `meta.sections` list of the
 * settings response: one entry per section, fields in definition order.
 */
final class SettingsMeta {

	/**
	 * Build the section list.
	 *
	 * @param array<string, array<string, mixed>> $sections     FieldsData::get_sections().
	 * @param array<string, string>               $descriptions FieldsData::get_descriptions().
	 * @return array<int, array{key: string, title: string, icon: string, fields: array<int, array{key: string, label: string, description: string}>}>
	 */
	public static function sections( array $sections, array $descriptions ): array {
		$list = array();

		foreach ( $sections as $key => $section ) {
			$fields = array();

			foreach ( (array) ( $section['fields'] ?? array() ) as $field => $label ) {
				$fields[] = array(
					'key'         => (string) $field,
					'label'       => (string) $label,
					'description' => (string) ( $descriptions[ $field ] ?? '' ),
				);
			}

			$list[] = array(
				'key'    => (string) $key,
				'title'  => (string) ( $section['title'] ?? '' ),
				'icon'   => (string) ( $section['icon'] ?? '' ),
				'fields' => $fields,
			);
		}

		return $list;
	}
}
