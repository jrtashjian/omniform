<?php
/**
 * Tests the CallbackSupport trait.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests;

/**
 * Tests the CallbackSupport trait.
 */
class CallbackSupportTest extends \WP_UnitTestCase {
	use \OmniForm\Traits\CallbackSupport;

	/**
	 * Test the has_callback method.
	 */
	public function test_has_callback() {
		$this->assertFalse( $this->has_callback( 'not a valid callback' ) );
		$this->assertTrue( $this->has_callback( '{{valid_callback}}' ) );
	}

	/**
	 * Data provider for test_process_callbacks.
	 */
	public static function provideCallbackData() {
		return array(
			array( 'no callbacks here', 'no callbacks here' ),
			array( '{{omniform_existent_callback_return_string}}', 'callback string' ),
			array( '{{omniform_existent_callback_return_array}}', '' ),
			array( '{{omniform_existent_callback_return_object}}', '' ),
			array( '{{omniform_existent_callback_return_false}}', '0' ),
			array( '{{omniform_existent_callback_return_true}}', '1' ),
			array( '{{omniform_existent_callback_return_number}}', '42' ),
			array( 'text before {{omniform_existent_callback_return_string}} text after', 'text before callback string text after' ),
			array( '{{omniform_existent_callback_return_string}} and {{omniform_existent_callback_return_number}}', 'callback string and 42' ),
			array( '{{omniform_existent_callback_return_string}} and {{omniform_existent_callback_return_true}}', 'callback string and 1' ),
			array( 'invalid {{callback', 'invalid {{callback' ),
		);
	}

	/**
	 * Test the process_callbacks method.
	 *
	 * @dataProvider provideCallbackData
	 *
	 * @param string $input    The input string to process.
	 * @param string $expected The expected output after processing callbacks.
	 */
	public function test_process_callbacks( $input, $expected ) {
		$result = $this->process_callbacks( $input );
		$this->assertIsString( $result );
		$this->assertEquals( $expected, $result );
	}
}
