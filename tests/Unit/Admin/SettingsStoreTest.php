<?php
/**
 * SettingsStore unit tests.
 *
 * @package LightweightPlugins\Enable
 */

declare(strict_types=1);

namespace LightweightPlugins\Enable\Tests\Unit\Admin;

use Brain\Monkey\Functions;
use LightweightPlugins\Enable\Admin\SettingsStore;
use LightweightPlugins\Enable\Options;
use LightweightPlugins\Enable\Tests\Unit\MonkeyTestCase;

/**
 * @covers \LightweightPlugins\Enable\Admin\SettingsStore
 * @covers \LightweightPlugins\Enable\Admin\SettingsSanitizer
 */
final class SettingsStoreTest extends MonkeyTestCase {

	private const DEFAULTS = array(
		'svg'   => false,
		'other' => true,
	);

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

	public function test_merge_keeps_stored_values_for_absent_keys(): void {
		$result = SettingsStore::merge( array( 'svg' => true ), array( 'other' => false ), self::DEFAULTS );

		$this->assertSame( array( 'svg' => true, 'other' => false ), $result );
	}

	public function test_merge_applies_submitted_false(): void {
		$result = SettingsStore::merge( array( 'svg' => false ), array( 'svg' => true ), self::DEFAULTS );

		$this->assertFalse( $result['svg'] );
	}

	public function test_merge_drops_unknown_keys(): void {
		$result = SettingsStore::merge(
			array( 'evil' => true, '_locale' => 'user' ),
			array( 'legacy' => 'y' ),
			self::DEFAULTS
		);

		$this->assertSame( array_keys( self::DEFAULTS ), array_keys( $result ) );
	}

	public function test_merge_falls_back_to_defaults_when_stored_is_not_an_array(): void {
		$this->assertSame( self::DEFAULTS, SettingsStore::merge( array(), 'garbage', self::DEFAULTS ) );
	}

	/**
	 * @dataProvider provide_submitted_values
	 *
	 * @param mixed $submitted Submitted value.
	 * @param bool  $expected  Stored value.
	 */
	public function test_merge_casts_submitted_values_to_bool( $submitted, bool $expected ): void {
		$result = SettingsStore::merge( array( 'svg' => $submitted ), array( 'svg' => ! $expected ), self::DEFAULTS );

		$this->assertSame( $expected, $result['svg'] );
	}

	/**
	 * @return array<string, array{0: mixed, 1: bool}>
	 */
	public static function provide_submitted_values(): array {
		return array(
			'true'          => array( true, true ),
			'int 1'         => array( 1, true ),
			'string 1'      => array( '1', true ),
			'string true'   => array( 'true', true ),
			'false'         => array( false, false ),
			'int 0'         => array( 0, false ),
			'string 0'      => array( '0', false ),
			'string false'  => array( 'false', false ),
			'empty string'  => array( '', false ),
			'null'          => array( null, false ),
		);
	}

	public function test_merge_keeps_current_value_for_array_input(): void {
		$result = SettingsStore::merge( array( 'svg' => array( true ) ), array( 'svg' => true ), self::DEFAULTS );

		$this->assertTrue( $result['svg'] );
	}

	public function test_merge_casts_stored_truthy_values_to_bool(): void {
		$result = SettingsStore::merge( array(), array( 'svg' => '1', 'other' => 0 ), self::DEFAULTS );

		$this->assertSame( array( 'svg' => true, 'other' => false ), $result );
	}

	public function test_typed_returns_every_default_key_as_bool(): void {
		$this->assertSame(
			array( 'svg' => true, 'other' => true ),
			SettingsStore::typed( array( 'svg' => 1, 'extra' => 'x' ), self::DEFAULTS )
		);
	}

	public function test_current_reads_the_stored_option(): void {
		Functions\when( 'get_option' )->justReturn( array( 'svg' => true ) );

		$this->assertSame( array( 'svg' => true ), SettingsStore::current() );
	}

	public function test_save_writes_the_merged_options(): void {
		$stored = array( 'svg' => false );
		Functions\when( 'get_option' )->alias( static function () use ( &$stored ) {
			return $stored;
		} );
		Functions\expect( 'update_option' )
			->once()
			->with( 'lw_enable', array( 'svg' => true ) )
			->andReturnUsing( static function ( $name, $value ) use ( &$stored ) {
				$stored = $value;
				return true;
			} );

		$result = SettingsStore::save( array( 'svg' => '1', 'unknown' => true ) );

		$this->assertSame( array( 'svg' => true ), $result );
	}
}
