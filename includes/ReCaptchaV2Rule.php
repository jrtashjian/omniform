<?php
/**
 * The ReCaptchaV2Rule class.
 *
 * @package OmniForm
 */

namespace OmniForm;

use OmniForm\Dependencies\Respect\Validation\Rules\AbstractRule;

/**
 * The ReCaptchaV2Rule class.
 */
class ReCaptchaV2Rule extends AbstractRule {
	/**
	 * Validates the input.
	 *
	 * @param mixed $input Input to validate.
	 *
	 * @return bool
	 */
	public function validate( $input ): bool {
		$secret = get_option( 'omniform_recaptchav2_secret_key' );

		if ( ! $secret ) {
			return true;
		}

		$response = wp_remote_post(
			'https://www.google.com/recaptcha/api/siteverify',
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
