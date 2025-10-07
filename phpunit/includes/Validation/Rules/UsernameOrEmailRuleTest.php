<?php
/**
 * The UsernameOrEmailRuleTest class.
 *
 * @package OmniForm
 */

namespace OmniForm\Validation\Rules;

use OmniForm\Validation\Rules\UsernameOrEmailRule;

/**
 * The UsernameOrEmailRuleTest class.
 */
class UsernameOrEmailRuleTest extends \WP_UnitTestCase {
	/**
	 * Test valid email.
	 */
	public function test_valid_email() {
		$rule = new UsernameOrEmailRule();
		$this->assertTrue( $rule->validate( 'test@example.com' ) );
	}

	/**
	 * Test valid username.
	 */
	public function test_valid_username() {
		$rule = new UsernameOrEmailRule();
		$this->assertTrue( $rule->validate( 'test_user-123' ) );
		$this->assertTrue( $rule->validate( 'user@' ) );
	}

	/**
	 * Test invalid input.
	 */
	public function test_invalid_input() {
		$rule = new UsernameOrEmailRule();
		$this->assertFalse( $rule->validate( '' ) );
	}
}
