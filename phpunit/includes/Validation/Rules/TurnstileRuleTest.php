<?php
/**
 * The TurnstileRuleTest class.
 *
 * @package OmniForm
 */

namespace OmniForm\Validation\Rules;

use OmniForm\Validation\Rules\TurnstileRule;

/**
 * The TurnstileRuleTest class.
 */
class TurnstileRuleTest extends \WP_UnitTestCase {
	/**
	 * Test validation fails when secret key is not configured.
	 */
	public function test_validation_fails_without_secret_key() {
		delete_option( 'omniform_turnstile_secret_key' );

		$rule = new TurnstileRule();
		$this->assertFalse( $rule->validate( 'test-response-token' ) );
	}

	/**
	 * Test validation fails when secret key is empty string.
	 */
	public function test_validation_fails_with_empty_secret_key() {
		update_option( 'omniform_turnstile_secret_key', '' );

		$rule = new TurnstileRule();
		$this->assertFalse( $rule->validate( 'test-response-token' ) );
	}
}
