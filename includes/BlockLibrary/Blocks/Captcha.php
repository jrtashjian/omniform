<?php
/**
 * The Captcha block class.
 *
 * @package OmniForm
 */

namespace OmniForm\BlockLibrary\Blocks;

/**
 * The Captcha block class.
 */
class Captcha extends BaseControlBlock {
	/**
	 * Renders the block on the server.
	 *
	 * @return string Returns the block content.
	 */
	public function render() {
		$service = $this->getBlockAttribute( 'service' );
		$theme   = $this->getBlockAttribute( 'theme' );
		$size    = $this->getBlockAttribute( 'size' );

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
					'id'           => $this->serviceSlug(),
					'class'        => 'hcaptcha' === $this->getBlockAttribute( 'service' )
						? 'h-captcha'
						: 'g-recaptcha',
					'data-service' => $service,
					'data-sitekey' => $site_key,
					'data-theme'   => $theme,
					'data-size'    => $size,
				)
			)
		);
	}

	public function renderControl() {}

	public function serviceSlug() {
		return 'hcaptcha' === $this->getBlockAttribute( 'service' )
			? 'hcaptcha'
			: 'recaptcha';
	}
}
