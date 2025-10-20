<?php
/**
 * Tests the HCaptchaRule class.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\Validation;

use OmniForm\Validation\Rules\HCaptchaRule;

/**
 * Tests the HCaptchaRule class.
 */
class HCaptchaRuleTest extends AbstractCaptchaRuleTest {
	/**
	 * Get the option key for the secret key.
	 *
	 * @return string
	 */
	protected function getOptionKey(): string {
		return 'omniform_hcaptcha_secret_key';
	}

	/**
	 * Get the verification URL.
	 *
	 * @return string
	 */
	protected function getVerifyUrl(): string {
		return 'https://hcaptcha.com/siteverify';
	}

	/**
	 * Get the rule class name.
	 *
	 * @return string
	 */
	protected function getRuleClass(): string {
		return HCaptchaRule::class;
	}
}
