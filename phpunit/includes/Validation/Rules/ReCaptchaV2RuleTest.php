<?php
/**
 * The ReCaptchaV2RuleTest class.
 *
 * @package OmniForm
 */

namespace OmniForm\Validation\Rules;

use OmniForm\Validation\Rules\ReCaptchaV2Rule;

/**
 * The ReCaptchaV2RuleTest class.
 */
class ReCaptchaV2RuleTest extends \WP_UnitTestCase {
	/**
	 * Test validation fails when secret key is not configured.
	 */
	public function test_validation_fails_without_secret_key() {
		delete_option( 'omniform_recaptchav2_secret_key' );

		$rule = new ReCaptchaV2Rule();
		$this->assertFalse( $rule->validate( 'test-response-token' ) );
	}

	/**
	 * Test validation fails when secret key is empty string.
	 */
	public function test_validation_fails_with_empty_secret_key() {
		update_option( 'omniform_recaptchav2_secret_key', '' );

		$rule = new ReCaptchaV2Rule();
		$this->assertFalse( $rule->validate( 'test-response-token' ) );
	}
}
