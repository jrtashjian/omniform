<?php
/**
 * The HCaptchaRuleTest class.
 *
 * @package OmniForm
 */

namespace OmniForm\Validation\Rules;

use OmniForm\Validation\Rules\HCaptchaRule;

/**
 * The HCaptchaRuleTest class.
 */
class HCaptchaRuleTest extends \WP_UnitTestCase {
	/**
	 * Test validation fails when secret key is not configured.
	 */
	public function test_validation_fails_without_secret_key() {
		delete_option( 'omniform_hcaptcha_secret_key' );

		$rule = new HCaptchaRule();
		$this->assertFalse( $rule->validate( 'test-response-token' ) );
	}

	/**
	 * Test validation fails when secret key is empty string.
	 */
	public function test_validation_fails_with_empty_secret_key() {
		update_option( 'omniform_hcaptcha_secret_key', '' );

		$rule = new HCaptchaRule();
		$this->assertFalse( $rule->validate( 'test-response-token' ) );
	}
}
