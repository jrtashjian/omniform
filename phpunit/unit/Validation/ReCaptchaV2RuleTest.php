<?php
/**
 * Tests the ReCaptchaV2Rule class.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\Validation;

use OmniForm\Validation\Rules\ReCaptchaV2Rule;

/**
 * Tests the ReCaptchaV2Rule class.
 */
class ReCaptchaV2RuleTest extends AbstractCaptchaRuleTest {
	/**
	 * Get the option key for the secret key.
	 *
	 * @return string
	 */
	protected function getOptionKey(): string {
		return 'omniform_recaptchav2_secret_key';
	}

	/**
	 * Get the verification URL.
	 *
	 * @return string
	 */
	protected function getVerifyUrl(): string {
		return 'https://www.google.com/recaptcha/api/siteverify';
	}

	/**
	 * Get the rule class name.
	 *
	 * @return string
	 */
	protected function getRuleClass(): string {
		return ReCaptchaV2Rule::class;
	}
}
