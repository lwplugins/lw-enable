<?php
/**
 * SettingsMeta unit tests.
 *
 * @package LightweightPlugins\Enable
 */

declare(strict_types=1);

namespace LightweightPlugins\Enable\Tests\Unit\Rest\Admin;

use LightweightPlugins\Enable\Rest\Admin\SettingsMeta;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LightweightPlugins\Enable\Rest\Admin\SettingsMeta
 */
final class SettingsMetaTest extends TestCase {

	public function test_sections_lists_sections_and_fields_in_order(): void {
		$sections = array(
			'media' => array(
				'title'  => 'Media',
				'icon'   => 'dashicons-admin-media',
				'fields' => array(
					'svg'  => 'SVG Uploads',
					'webp' => 'WebP',
				),
			),
		);

		$result = SettingsMeta::sections( $sections, array( 'svg' => 'Allow SVG' ) );

		$this->assertSame(
			array(
				array(
					'key'    => 'media',
					'title'  => 'Media',
					'icon'   => 'dashicons-admin-media',
					'fields' => array(
						array(
							'key'         => 'svg',
							'label'       => 'SVG Uploads',
							'description' => 'Allow SVG',
						),
						array(
							'key'         => 'webp',
							'label'       => 'WebP',
							'description' => '',
						),
					),
				),
			),
			$result
		);
	}

	public function test_sections_returns_empty_list_without_sections(): void {
		$this->assertSame( array(), SettingsMeta::sections( array(), array() ) );
	}
}
