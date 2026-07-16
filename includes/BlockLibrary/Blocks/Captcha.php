<?php
/**
 * The Captcha block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

use OmniForm\Dependencies\Respect\Validation;
use OmniForm\Validation\Rules\HCaptchaRule;
use OmniForm\Validation\Rules\ReCaptchaV2Rule;
use OmniForm\Validation\Rules\ReCaptchaV3Rule;
use OmniForm\Validation\Rules\TurnstileRule;

/**
 * The Captcha block class.
 */
class Captcha extends BaseControlBlock {
	/**
	 * An array of available CAPTCHA services.
	 */
	const SERVICES = array(
		'hcaptcha'    => 'hCaptcha',
		'recaptchav2' => 'reCAPTCHA',
		'recaptchav3' => 'reCAPTCHA',
		'turnstile'   => 'Turnstile',
	);

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
	public function render(): string {
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
	public function get_field_label(): ?string {
		return self::SERVICES[ $this->get_block_attribute( 'service' ) ] ?? null;
	}

	/**
	 * Gets the field name (sanitized).
	 *
	 * @return string
	 */
	public function get_field_name(): string {
		return match ( $this->get_block_attribute( 'service' ) ) {
			'hcaptcha' => 'h-captcha-response',
			'recaptchav2', 'recaptchav3' => 'g-recaptcha-response',
			'turnstile' => 'cf-turnstile-response',
			default => '',
		};
	}

	/**
	 * Gets the field group name (sanitized).
	 *
	 * @return string
	 */
	public function get_field_group_name(): string {
		return '';
	}

	/**
	 * Get the validation rules for the field.
	 *
	 * @return list<object>
	 */
	public function get_validation_rules(): array {
		$rule = match ( $this->get_block_attribute( 'service' ) ) {
			'hcaptcha' => new HCaptchaRule(),
			'recaptchav2' => new ReCaptchaV2Rule(),
			'recaptchav3' => new ReCaptchaV3Rule(),
			'turnstile' => new TurnstileRule(),
			default => null,
		};

		return array_values(
			array_filter(
				array(
					new Validation\Rules\NotEmpty(),
					$rule,
				)
			)
		);
	}

	/**
	 * Renders the form control.
	 *
	 * @return string
	 */
	public function render_control(): string {
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
