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
		$theme   = $this->get_block_attribute( 'theme' );
		$size    = $this->get_block_attribute( 'size' );

		$site_key = get_option( 'omniform_' . $service . '_site_key' );

		$service_urls = array(
			'hcaptcha'    => 'https://js.hcaptcha.com/1/api.js?render=explicit&onload=omniformCaptchaOnLoad',
			'recaptchav2' => 'https://www.google.com/recaptcha/api.js?render=explicit&onload=omniformCaptchaOnLoad',
			'recaptchav3' => 'https://www.google.com/recaptcha/api.js?render=' . $site_key,
		);

		wp_enqueue_script(
			'omniform-' . $service,
			$service_urls[ $service ],
			array(),
			omniform()->version(),
			true
		);

		return sprintf(
			'<div %s></div>',
			get_block_wrapper_attributes(
				array(
					'id'           => 'hcaptcha' === $this->get_block_attribute( 'service' ) ? 'hcaptcha' : 'recaptcha',
					'class'        => 'hcaptcha' === $this->get_block_attribute( 'service' ) ? 'h-captcha' : 'g-recaptcha',
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
			'hcaptcha'    => __( 'hCaptcha', 'omniform' ),
			'recaptchav2' => __( 'reCAPTCHA', 'omniform' ),
			'recaptchav3' => __( 'reCAPTCHA', 'omniform' ),
		);

		return $service_labels[ $this->get_block_attribute( 'service' ) ];
	}

	/**
	 * Gets the field name (sanitized).
	 *
	 * @return string|null
	 */
	public function get_field_name() {
		return 'hcaptcha' === $this->get_block_attribute( 'service' )
		? 'h-captcha-response'
		: 'g-recaptcha-response';
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

		return $filtered_request_params;
	}
}
