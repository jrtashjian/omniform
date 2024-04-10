<?php
/**
 * The Captcha block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

use OmniForm\Dependencies\Respect\Validation;
use OmniForm\HCaptchaRule;
use OmniForm\ReCaptchaV2Rule;
use OmniForm\ReCaptchaV3Rule;
use OmniForm\TurnstileRule;

/**
 * The Captcha block class.
 */
class Captcha extends BaseControlBlock {
	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'omniform_filtered_request_params', array( $this, 'filter_request_params' ) );
	}

	/**
	 * Renders the block on the server.
	 *
	 * @return string Returns the block content.
	 */
	public function render() {
		$service = $this->get_block_attribute( 'service' );

		if ( empty( $service ) ) {
			return '';
		}

		$theme = $this->get_block_attribute( 'theme' );
		$size  = $this->get_block_attribute( 'size' );

		$site_key = get_option( 'omniform_' . $service . '_site_key' );

		switch ( $this->get_block_attribute( 'service' ) ) {
			case 'hcaptcha':
				$service_url = 'https://js.hcaptcha.com/1/api.js?render=explicit&onload=omniformCaptchaOnLoad';
				$classname   = 'h-captcha';
				break;
			case 'recaptchav2':
				$service_url = 'https://www.google.com/recaptcha/api.js?render=explicit&onload=omniformCaptchaOnLoad';
				$classname   = 'g-recaptcha';
				break;
			case 'recaptchav3':
				$service_url = 'https://www.google.com/recaptcha/api.js?render=' . $site_key;
				$classname   = 'g-recaptcha';
				break;
			case 'turnstile':
				$service_url = 'https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit&onload=omniformCaptchaOnLoad';
				$classname   = 'cf-turnstile';
				break;
			default:
				$service_url = '';
				$classname   = '';
				break;
		}

		if ( empty( $service_url ) ) {
			return '';
		}

		wp_enqueue_script(
			'omniform-' . $service,
			$service_url,
			array(),
			omniform()->version(),
			true
		);

		return sprintf(
			'<div %s></div>',
			get_block_wrapper_attributes(
				array(
					'class'        => $classname,
					'data-service' => $service,
					'data-sitekey' => $site_key,
					'data-theme'   => $theme,
					'data-size'    => $size,
				)
			)
		);
	}

	/**
	 * Gets the field label.
	 *
	 * @return string|null
	 */
	public function get_field_label() {
		$service_labels = array(
			'hcaptcha'    => esc_attr__( 'hCaptcha', 'omniform' ),
			'recaptchav2' => esc_attr__( 'reCAPTCHA', 'omniform' ),
			'recaptchav3' => esc_attr__( 'reCAPTCHA', 'omniform' ),
			'turnstile'   => esc_attr__( 'Turnstile', 'omniform' ),
		);

		return $service_labels[ $this->get_block_attribute( 'service' ) ];
	}

	/**
	 * Gets the field name (sanitized).
	 *
	 * @return string|null
	 */
	public function get_field_name() {
		switch ( $this->get_block_attribute( 'service' ) ) {
			case 'hcaptcha':
				$fieldname = 'h-captcha-response';
				break;
			case 'recaptchav2':
			case 'recaptchav3':
				$fieldname = 'g-recaptcha-response';
				break;
			case 'turnstile':
				$fieldname = 'cf-turnstile-response';
				break;
		}
		return $fieldname;
	}

	/**
	 * Gets the field group name (sanitized).
	 *
	 * @return string|null
	 */
	public function get_field_group_name() {
		return null;
	}

	/**
	 * Get the validation rules for the field.
	 *
	 * @return array
	 */
	public function get_validation_rules() {
		switch ( $this->get_block_attribute( 'service' ) ) {
			case 'hcaptcha':
				$rule = new HCaptchaRule();
				break;
			case 'recaptchav2':
				$rule = new ReCaptchaV2Rule();
				break;
			case 'recaptchav3':
				$rule = new ReCaptchaV3Rule();
				break;
			case 'turnstile':
				$rule = new TurnstileRule();
				break;
		}

		return array_filter(
			array(
				new Validation\Rules\NotEmpty(),
				$rule,
			)
		);
	}

	/**
	 * Renders the form control.
	 *
	 * @return string
	 */
	public function render_control() {
		// Don't render a control for CAPTCHA.
		return '';
	}

	/**
	 * Filters the request params.
	 *
	 * @param array $filtered_request_params The filtered request params.
	 *
	 * @return array
	 */
	public function filter_request_params( $filtered_request_params ) {
		$filtered_request_params[] = 'g-recaptcha-response';
		$filtered_request_params[] = 'h-captcha-response';
		$filtered_request_params[] = 'cf-turnstile-response';

		return $filtered_request_params;
	}
}
