<?php
/**
 * Tests ValidationResult.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\Form;

use OmniForm\Form\ValidationResult;
use OmniForm\Tests\Unit\BaseTestCase;

/**
 * Tests ValidationResult.
 */
class ValidationResultTest extends BaseTestCase {
	/**
	 * Valid result has no messages.
	 */
	public function testValid() {
		$result = ValidationResult::valid();

		$this->assertTrue( $result->is_valid() );
		$this->assertFalse( $result->is_invalid() );
		$this->assertSame( array(), $result->messages() );
	}

	/**
	 * Invalid result exposes messages.
	 */
	public function testInvalid() {
		$result = ValidationResult::invalid( array( 'email' => 'Email is required' ) );

		$this->assertTrue( $result->is_invalid() );
		$this->assertFalse( $result->is_valid() );
		$this->assertSame( array( 'email' => 'Email is required' ), $result->messages() );
	}
}
