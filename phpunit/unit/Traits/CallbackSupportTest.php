<?php
/**
 * Tests the CallbackSupport trait.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\Traits;

use WP_Mock;
use OmniForm\Tests\Unit\BaseTestCase;
use OmniForm\Traits\CallbackSupport;

// Include callback functions for testing.
require_once __DIR__ . '/callback-functions.php';

/**
 * Tests the CallbackSupport trait.
 */
class CallbackSupportTest extends BaseTestCase {

	/**
	 * Test has_callback returns true when content contains callback placeholder.
	 */
	public function testHasCallbackReturnsTrueWhenPlaceholderExists() {
		$dummy = new DummyCallbackSupport();
		$this->assertTrue( $dummy->public_has_callback( 'Some text with {{callback}}' ) );
		$this->assertTrue( $dummy->public_has_callback( '{{callback}} at start' ) );
		$this->assertTrue( $dummy->public_has_callback( 'At end {{callback}}' ) );
	}

	/**
	 * Test has_callback returns false when content does not contain callback placeholder.
	 */
	public function testHasCallbackReturnsFalseWhenPlaceholderDoesNotExist() {
		$dummy = new DummyCallbackSupport();
		$this->assertFalse( $dummy->public_has_callback( 'Some text without placeholders' ) );
		$this->assertFalse( $dummy->public_has_callback( 'Text with {single braces}' ) );
		$this->assertFalse( $dummy->public_has_callback( '' ) );
	}

	/**
	 * Test process_callbacks returns content unchanged when no callbacks exist.
	 */
	public function testProcessCallbacksReturnsUnchangedWhenNoCallbacks() {
		$dummy   = new DummyCallbackSupport();
		$content = 'Some content without callbacks';
		$this->assertEquals( $content, $dummy->public_process_callbacks( $content ) );
	}

	/**
	 * Test process_callbacks replaces callback with string result.
	 */
	public function testProcessCallbacksReplacesCallbackWithStringResult() {
		WP_Mock::userFunction(
			'esc_attr',
			array(
				'args'   => array( 'callback string' ),
				'return' => 'callback string',
			)
		);

		$dummy  = new DummyCallbackSupport();
		$result = $dummy->public_process_callbacks( 'Result: {{ omniform_existent_callback_return_string }}' );
		$this->assertEquals( 'Result: callback string', $result );
	}

	/**
	 * Test process_callbacks replaces callback with boolean result converted to string.
	 */
	public function testProcessCallbacksReplacesCallbackWithBooleanResult() {
		WP_Mock::userFunction(
			'esc_attr',
			array(
				'args'   => array( '1' ),
				'return' => '1',
			)
		);

		$dummy  = new DummyCallbackSupport();
		$result = $dummy->public_process_callbacks( '{{ omniform_existent_callback_return_true }}' );
		$this->assertEquals( '1', $result );
	}

	/**
	 * Test process_callbacks replaces callback with numeric result converted to string.
	 */
	public function testProcessCallbacksReplacesCallbackWithNumericResult() {
		WP_Mock::userFunction(
			'esc_attr',
			array(
				'args'   => array( '42' ),
				'return' => '42',
			)
		);

		$dummy  = new DummyCallbackSupport();
		$result = $dummy->public_process_callbacks( '{{ omniform_existent_callback_return_number }}' );
		$this->assertEquals( '42', $result );
	}

	/**
	 * Test process_callbacks returns empty string when callback returns non-string.
	 */
	public function testProcessCallbacksReturnsEmptyStringForNonStringResult() {
		$dummy  = new DummyCallbackSupport();
		$result = $dummy->public_process_callbacks( '{{ omniform_existent_callback_return_array }}' );
		$this->assertEquals( '', $result );
	}

	/**
	 * Test process_callbacks returns empty string when callback function does not exist.
	 */
	public function testProcessCallbacksReturnsEmptyStringWhenFunctionDoesNotExist() {
		$dummy  = new DummyCallbackSupport();
		$result = $dummy->public_process_callbacks( '{{ nonexistent_callback }}' );
		$this->assertEquals( '', $result );
	}

	/**
	 * Test process_callbacks handles multiple callbacks.
	 */
	public function testProcessCallbacksHandlesMultipleCallbacks() {
		WP_Mock::userFunction(
			'esc_attr',
			array(
				'args'   => array( 'callback string' ),
				'return' => 'callback string',
				'times'  => 1,
			)
		);

		WP_Mock::userFunction(
			'esc_attr',
			array(
				'args'   => array( '42' ),
				'return' => '42',
				'times'  => 1,
			)
		);

		$dummy  = new DummyCallbackSupport();
		$result = $dummy->public_process_callbacks( 'String: {{ omniform_existent_callback_return_string }} Number: {{ omniform_existent_callback_return_number }}' );
		$this->assertEquals( 'String: callback string Number: 42', $result );
	}

	/**
	 * Test process_callbacks handles callbacks with extra whitespace.
	 */
	public function testProcessCallbacksHandlesCallbacksWithWhitespace() {
		WP_Mock::userFunction(
			'esc_attr',
			array(
				'args'   => array( '8.3.29' ),
				'return' => '8.3.29',
			)
		);

		$dummy  = new DummyCallbackSupport();
		$result = $dummy->public_process_callbacks( '{{  phpversion  }}' );
		$this->assertEquals( '8.3.29', $result );
	}
}

// phpcs:disable
/**
 * Dummy class to test the CallbackSupport trait.
 */
class DummyCallbackSupport {
	use CallbackSupport;

	/**
	 * Public wrapper for has_callback.
	 *
	 * @param string $content The content to check.
	 * @return bool
	 */
	public function public_has_callback( $content ) {
		return $this->has_callback( $content );
	}

	/**
	 * Public wrapper for process_callbacks.
	 *
	 * @param string $content The content to process.
	 * @return string
	 */
	public function public_process_callbacks( $content ) {
		return $this->process_callbacks( $content );
	}
}
