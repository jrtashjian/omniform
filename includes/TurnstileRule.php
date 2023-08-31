<?php
/**
 * The TurnstileRule class.
 *
 * @package OmniForm
 */

namespace OmniForm;

use OmniForm\Dependencies\Respect\Validation\Rules\AbstractRule;

/**
 * The TurnstileRule class.
 */
class TurnstileRule extends AbstractRule {
	/**
	 * Validates the input.
	 *
	 * @param mixed $input Input to validate.
	 *
	 * @return bool
	 */
	public function validate( $input ): bool {
		$secret = get_option( 'omniform_turnstile_secret_key' );

		if ( ! $secret ) {
			return true;
		}

		$response = wp_remote_post(
			'https://challenges.cloudflare.com/turnstile/v0/siteverify',
			array(
				'body' => array(
					'secret'   => $secret,
					'response' => $input,
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$response = json_decode( wp_remote_retrieve_body( $response ), true );

		return $response['success'];
	}
}
