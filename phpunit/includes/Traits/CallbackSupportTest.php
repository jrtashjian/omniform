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
	 * Test the process_callbacks method.
	 */
	public function test_process_callbacks() {
		// No callbacks.
		$this->assertEquals( 'no callbacks here', $this->process_callbacks( 'no callbacks here' ) );

		// Single callback.
		$callback_result = $this->process_callbacks( '{{omniform_existent_callback_return_string}}' );
		$this->assertIsString( $callback_result );
		$this->assertEquals( 'callback string', $callback_result );

		// Callback returning array.
		$callback_result = $this->process_callbacks( '{{omniform_existent_callback_return_array}}' );
		$this->assertIsString( $callback_result );
		$this->assertEquals( '', $callback_result );

		// Callback returning object.
		$callback_result = $this->process_callbacks( '{{omniform_existent_callback_return_object}}' );
		$this->assertIsString( $callback_result );
		$this->assertEquals( '', $callback_result );

		// Callback returning false.
		$callback_result = $this->process_callbacks( '{{omniform_existent_callback_return_false}}' );
		$this->assertIsString( $callback_result );
		$this->assertEquals( '0', $callback_result );

		// Callback returning true.
		$callback_result = $this->process_callbacks( '{{omniform_existent_callback_return_true}}' );
		$this->assertIsString( $callback_result );
		$this->assertEquals( '1', $callback_result );

		// Callback returning number.
		$callback_result = $this->process_callbacks( '{{omniform_existent_callback_return_number}}' );
		$this->assertIsString( $callback_result );
		$this->assertEquals( '42', $callback_result );

		// Callback within text.
		$callback_result = $this->process_callbacks( 'text before {{omniform_existent_callback_return_string}} text after' );
		$this->assertIsString( $callback_result );
		$this->assertEquals( 'text before callback string text after', $callback_result );

		// Multiple callbacks.
		$callback_result = $this->process_callbacks( '{{omniform_existent_callback_return_string}} and {{omniform_existent_callback_return_number}}' );
		$this->assertIsString( $callback_result );
		$this->assertEquals( 'callback string and 42', $callback_result );

		// Nested callbacks (if applicable).
		$callback_result = $this->process_callbacks( '{{omniform_existent_callback_return_string}} and {{omniform_existent_callback_return_true}}' );
		$this->assertIsString( $callback_result );
		$this->assertEquals( 'callback string and 1', $callback_result );

		// Invalid callback format.
		$callback_result = $this->process_callbacks( 'invalid {{callback' );
		$this->assertIsString( $callback_result );
		$this->assertEquals( 'invalid {{callback', $callback_result );
	}
}
