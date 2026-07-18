<?php
/**
 * Characterization tests for the SVG Sanitizer.
 *
 * These pin down the security boundary: which SVG payloads are accepted and
 * which are rejected. Sanitizer::is_valid() uses only PHP built-ins (no
 * WordPress functions), so these are plain unit tests without Brain Monkey.
 *
 * @package LightweightPlugins\Enable
 */

declare(strict_types=1);

namespace LightweightPlugins\Enable\Tests\Unit\Svg;

use LightweightPlugins\Enable\Svg\Sanitizer;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LightweightPlugins\Enable\Svg\Sanitizer
 */
final class SanitizerTest extends TestCase {

	private const VALID_SVG = '<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10"><rect width="10" height="10"/></svg>';

	public function test_accepts_a_clean_minimal_svg(): void {
		$this->assertTrue( Sanitizer::is_valid( self::VALID_SVG ) );
	}

	public function test_rejects_empty_content(): void {
		$this->assertFalse( Sanitizer::is_valid( '' ) );
	}

	public function test_rejects_content_that_is_not_an_svg(): void {
		$this->assertFalse( Sanitizer::is_valid( '<div>hello</div>' ) );
	}

	public function test_rejects_svg_without_a_closing_tag(): void {
		$this->assertFalse( Sanitizer::is_valid( '<svg xmlns="http://www.w3.org/2000/svg">' ) );
	}

	public function test_rejects_malformed_xml(): void {
		// Structurally SVG-looking and pattern-clean, but not well-formed.
		$this->assertFalse(
			Sanitizer::is_valid( '<svg xmlns="http://www.w3.org/2000/svg"><g></svg>' )
		);
	}

	/**
	 * Each payload is a real SVG-based XSS/XXE vector that must be rejected.
	 *
	 * @dataProvider provide_dangerous_svgs
	 */
	public function test_rejects_dangerous_svg( string $svg ): void {
		$this->assertFalse( Sanitizer::is_valid( $svg ) );
	}

	/**
	 * @return array<string, array{0: string}>
	 */
	public static function provide_dangerous_svgs(): array {
		$ns = 'xmlns="http://www.w3.org/2000/svg"';

		return array(
			'inline script'      => array( "<svg {$ns}><script>alert(1)</script></svg>" ),
			'event handler'      => array( "<svg {$ns} onload=\"alert(1)\"></svg>" ),
			'javascript uri'     => array( "<svg {$ns}><a href=\"javascript:alert(1)\">x</a></svg>" ),
			'iframe element'     => array( "<svg {$ns}><iframe src=\"x\"></iframe></svg>" ),
			'foreignObject'      => array( "<svg {$ns}><foreignObject></foreignObject></svg>" ),
			'external use href'  => array( "<svg {$ns}><use href=\"https://evil.example/x\"/></svg>" ),
			'doctype'            => array( "<!DOCTYPE svg><svg {$ns}></svg>" ),
			'entity declaration' => array( "<!DOCTYPE svg [<!ENTITY xxe \"boom\">]><svg {$ns}></svg>" ),
			'inline style'       => array( "<svg {$ns}><style>a{background:url(1)}</style></svg>" ),
		);
	}
}
