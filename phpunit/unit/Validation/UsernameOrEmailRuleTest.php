<?php
/**
 * Tests the UsernameOrEmailRule class.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\Validation;

use WP_Mock;
use OmniForm\Tests\Unit\BaseTestCase;
use OmniForm\Validation\Rules\UsernameOrEmailRule;

/**
 * Tests the UsernameOrEmailRule class.
 */
class UsernameOrEmailRuleTest extends BaseTestCase {
	/**
	 * Test validate method returns true for valid email.
	 */
	public function testValidateReturnsTrueForValidEmail() {
		$rule = new UsernameOrEmailRule();
		$this->assertTrue( $rule->validate( 'test@example.com' ) );
	}

	/**
	 * Test validate method returns true for valid username.
	 */
	public function testValidateReturnsTrueForValidUsername() {
		WP_Mock::userFunction(
			'validate_username',
			array(
				'args'   => array( 'validuser' ),
				'return' => true,
			)
		);

		$rule = new UsernameOrEmailRule();
		$this->assertTrue( $rule->validate( 'validuser' ) );
	}

	/**
	 * Test validate method returns false for invalid input.
	 */
	public function testValidateReturnsFalseForInvalidInput() {
		WP_Mock::userFunction(
			'validate_username',
			array(
				'args'   => array( 'invalid@input' ),
				'return' => false,
			)
		);

		$rule = new UsernameOrEmailRule();
		$this->assertFalse( $rule->validate( 'invalid@input' ) );
	}

	/**
	 * Test validate method returns false for empty string.
	 */
	public function testValidateReturnsFalseForEmptyString() {
		WP_Mock::userFunction(
			'validate_username',
			array(
				'args'   => array( '' ),
				'return' => false,
			)
		);

		$rule = new UsernameOrEmailRule();
		$this->assertFalse( $rule->validate( '' ) );
	}

	/**
	 * Test validate method returns false for null input.
	 */
	public function testValidateReturnsFalseForNullInput() {
		WP_Mock::userFunction(
			'validate_username',
			array(
				'args'   => array( null ),
				'return' => false,
			)
		);

		$rule = new UsernameOrEmailRule();
		$this->assertFalse( $rule->validate( null ) );
	}

	/**
	 * Test validate method prioritizes email validation over username.
	 */
	public function testValidatePrioritizesEmailOverUsername() {
		// Even if validate_username would return true, email validation should take precedence.
		$rule = new UsernameOrEmailRule();
		$this->assertTrue( $rule->validate( 'user@domain.com' ) );
	}
}
