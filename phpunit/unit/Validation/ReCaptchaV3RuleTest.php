<?php
/**
 * Tests the ReCaptchaV3Rule class.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\Validation;

use OmniForm\Validation\Rules\ReCaptchaV3Rule;

/**
 * Tests the ReCaptchaV3Rule class.
 */
class ReCaptchaV3RuleTest extends AbstractCaptchaRuleTest {
	/**
	 * Get the option key for the secret key.
	 *
	 * @return string
	 */
	protected function getOptionKey(): string {
		return 'omniform_recaptchav3_secret_key';
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
		return ReCaptchaV3Rule::class;
	}
}
