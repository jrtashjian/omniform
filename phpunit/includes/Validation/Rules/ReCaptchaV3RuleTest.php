<?php
/**
 * The ReCaptchaV3RuleTest class.
 *
 * @package OmniForm
 */

namespace OmniForm\Validation\Rules;

use OmniForm\Validation\Rules\ReCaptchaV3Rule;

/**
 * The ReCaptchaV3RuleTest class.
 */
class ReCaptchaV3RuleTest extends \WP_UnitTestCase {
	/**
	 * Test validation fails when secret key is not configured.
	 */
	public function test_validation_fails_without_secret_key() {
		delete_option( 'omniform_recaptchav3_secret_key' );

		$rule = new ReCaptchaV3Rule();
		$this->assertFalse( $rule->validate( 'test-response-token' ) );
	}

	/**
	 * Test validation fails when secret key is empty string.
	 */
	public function test_validation_fails_with_empty_secret_key() {
		update_option( 'omniform_recaptchav3_secret_key', '' );

		$rule = new ReCaptchaV3Rule();
		$this->assertFalse( $rule->validate( 'test-response-token' ) );
	}
}
