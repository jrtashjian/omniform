<?php
/**
 * Tests the TurnstileRule class.
 *
 * @package OmniForm
 */

namespace OmniForm\Tests\Unit\Validation;

use OmniForm\Validation\Rules\TurnstileRule;

/**
 * Tests the TurnstileRule class.
 */
class TurnstileRuleTest extends AbstractCaptchaRuleTest {
	/**
	 * Get the option key for the secret key.
	 *
	 * @return string
	 */
	protected function getOptionKey(): string {
		return 'omniform_turnstile_secret_key';
	}

	/**
	 * Get the verification URL.
	 *
	 * @return string
	 */
	protected function getVerifyUrl(): string {
		return 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
	}

	/**
	 * Get the rule class name.
	 *
	 * @return string
	 */
	protected function getRuleClass(): string {
		return TurnstileRule::class;
	}
}
