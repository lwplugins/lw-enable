<?php
/**
 * Characterization tests for Options.
 *
 * Pins down the current option-resolution behaviour (see .claude/rules/tests.md).
 *
 * @package LightweightPlugins\Enable
 */

declare(strict_types=1);

namespace LightweightPlugins\Enable\Tests\Unit;

use Brain\Monkey\Functions;
use LightweightPlugins\Enable\Options;

/**
 * @covers \LightweightPlugins\Enable\Options
 */
final class OptionsTest extends MonkeyTestCase {

	protected function setUp(): void {
		parent::setUp();
		Options::clear_cache();

		Functions\when( 'wp_parse_args' )->alias(
			static fn( $args, $defaults = array() ) => array_merge( (array) $defaults, (array) $args )
		);
	}

	protected function tearDown(): void {
		Options::clear_cache();
		parent::tearDown();
	}

	public function test_svg_default_is_disabled(): void {
		$defaults = Options::get_defaults();

		$this->assertArrayHasKey( 'svg', $defaults );
		$this->assertFalse( $defaults['svg'] );
	}

	public function test_get_returns_false_when_nothing_saved(): void {
		Functions\when( 'get_option' )->justReturn( array() );

		$this->assertFalse( Options::get( 'svg' ) );
	}

	public function test_get_returns_true_for_saved_truthy_value(): void {
		Functions\when( 'get_option' )->justReturn( array( 'svg' => true ) );

		$this->assertTrue( Options::get( 'svg' ) );
	}

	public function test_get_returns_false_for_an_unknown_key(): void {
		Functions\when( 'get_option' )->justReturn( array() );

		$this->assertFalse( Options::get( 'not_a_real_option' ) );
	}

	public function test_get_all_merges_saved_over_defaults(): void {
		Functions\when( 'get_option' )->justReturn( array( 'svg' => true ) );

		$all = Options::get_all();

		$this->assertTrue( $all['svg'] );
		$this->assertArrayHasKey( 'svg', $all );
	}

	public function test_get_all_is_cached(): void {
		Functions\when( 'get_option' )->justReturn( array( 'svg' => true ) );
		$first = Options::get_all();

		// A different backing value must NOT be seen until the cache is cleared.
		Functions\when( 'get_option' )->justReturn( array( 'svg' => false ) );
		$this->assertSame( $first, Options::get_all() );

		Options::clear_cache();
		$this->assertFalse( Options::get_all()['svg'] );
	}
}
