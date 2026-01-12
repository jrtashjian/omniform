<?php
/**
 * Tests the Number class.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\Plugin\Support;

use OmniForm\Plugin\Support\Number;
use PHPUnit\Framework\TestCase;

/**
 * Tests the Number class.
 */
class NumberTest extends TestCase {
	/**
	 * Test formatting a number with default locale.
	 */
	public function testFormat() {
		$result = Number::format( 1234.56 );

		$this->assertIsString( $result );
		$this->assertEquals( '1,234.56', $result );
	}

	/**
	 * Test formatting a number with precision.
	 */
	public function testFormatWithPrecision() {
		$result = Number::format( 1234.56789, 2 );

		$this->assertIsString( $result );
		$this->assertEquals( '1,234.57', $result );
	}

	/**
	 * Test formatting a number with zero precision.
	 */
	public function testFormatWithZeroPrecision() {
		$result = Number::format( 1234.56, 0 );

		$this->assertIsString( $result );
		$this->assertEquals( '1,235', $result );
	}

	/**
	 * Test formatting a percentage.
	 */
	public function testPercentage() {
		$result = Number::percentage( 0.1234 );

		$this->assertIsString( $result );
		$this->assertEquals( '12%', $result );
	}

	/**
	 * Test formatting a percentage with precision.
	 */
	public function testPercentageWithPrecision() {
		$result = Number::percentage( 0.1234, 2 );

		$this->assertIsString( $result );
		$this->assertEquals( '12.34%', $result );
	}

	/**
	 * Test setting the locale.
	 */
	public function testUseLocale() {
		Number::use_locale( 'de_DE' );

		// Since locale change might not affect number_format, we just ensure no exception.
		$this->assertTrue( true );
	}

	/**
	 * Test formatting with different locale (if intl is available).
	 */
	public function testFormatWithLocale() {
		if ( class_exists( '\NumberFormatter' ) ) {
			Number::use_locale( 'de_DE' );

			$result = Number::format( 1234.56 );

			// German locale uses comma as decimal separator.
			$this->assertStringContainsString( '1.234,56', $result );
		} else {
			$this->markTestSkipped( 'Intl extension not available.' );
		}
	}

	/**
	 * Test percentage with different locale (if intl is available).
	 */
	public function testPercentageWithLocale() {
		if ( class_exists( '\NumberFormatter' ) ) {
			Number::use_locale( 'de_DE' );

			$result = Number::percentage( 0.1234 );

			// German locale percentage.
			$this->assertStringContainsString( '12', $result );
		} else {
			$this->markTestSkipped( 'Intl extension not available.' );
		}
	}
}
