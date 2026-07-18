<?php
/**
 * Characterization tests for the SVG MimeType helpers.
 *
 * These methods are pure array/string transforms (no WordPress functions),
 * so they run as plain unit tests without Brain Monkey.
 *
 * @package LightweightPlugins\Enable
 */

declare(strict_types=1);

namespace LightweightPlugins\Enable\Tests\Unit\Svg;

use LightweightPlugins\Enable\Svg\MimeType;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LightweightPlugins\Enable\Svg\MimeType
 */
final class MimeTypeTest extends TestCase {

	private MimeType $mime;

	protected function setUp(): void {
		parent::setUp();
		$this->mime = new MimeType();
	}

	public function test_add_svg_registers_the_svg_mime_type(): void {
		$result = $this->mime->add_svg( array( 'png' => 'image/png' ) );

		$this->assertSame( 'image/svg+xml', $result['svg'] );
		$this->assertSame( 'image/png', $result['png'] );
	}

	public function test_add_image_size_ext_maps_mime_to_extension(): void {
		$result = $this->mime->add_image_size_ext( array() );

		$this->assertSame( 'svg', $result['image/svg+xml'] );
	}

	public function test_check_filetype_fills_in_data_for_an_svg_filename(): void {
		$result = $this->mime->check_filetype( array(), '/tmp/x', 'logo.svg', null );

		$this->assertSame( 'svg', $result['ext'] );
		$this->assertSame( 'image/svg+xml', $result['type'] );
		$this->assertSame( 'logo.svg', $result['proper_filename'] );
	}

	public function test_check_filetype_leaves_non_svg_data_untouched(): void {
		$data   = array( 'ext' => '', 'type' => '' );
		$result = $this->mime->check_filetype( $data, '/tmp/x', 'photo.png', null );

		$this->assertSame( $data, $result );
	}

	public function test_allow_image_mime_is_true_for_svg_filenames(): void {
		$this->assertTrue( $this->mime->allow_image_mime( false, '/tmp/x', 'icon.svg' ) );
	}

	public function test_allow_image_mime_preserves_a_prior_true_result(): void {
		$this->assertTrue( $this->mime->allow_image_mime( true, '/tmp/x', 'photo.png' ) );
	}

	public function test_allow_image_mime_is_false_for_non_svg_without_prior_match(): void {
		$this->assertFalse( $this->mime->allow_image_mime( false, '/tmp/x', 'photo.png' ) );
	}
}
