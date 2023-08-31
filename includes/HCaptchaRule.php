<?php
/**
 * The HCaptchaRule class.
 *
 * @package OmniForm
 */

namespace OmniForm;

use OmniForm\Dependencies\Respect\Validation\Rules\AbstractRule;

/**
 * The HCaptchaRule class.
 */
class HCaptchaRule extends AbstractRule {
	/**
	 * Validates the input.
	 *
	 * @param mixed $input Input to validate.
	 *
	 * @return bool
	 */
	public function validate( $input ): bool {
		$secret = get_option( 'omniform_hcaptcha_secret_key' );

		if ( ! $secret ) {
			return true;
		}

		$response = wp_remote_post(
			'https://hcaptcha.com/siteverify',
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
