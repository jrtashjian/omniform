/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import {
	iconReCaptcha,
	iconHCaptcha,
	iconTurnstile,
} from '../shared/icons';

const variations = [
	{
		name: 'captcha-recaptchav2',
		icon: { src: iconReCaptcha },
		title: __( 'reCAPTCHA v2', 'omniform' ),
		attributes: { service: 'recaptchav2', size: 'normal' },
		isDefault: true,
	},
	{
		name: 'captcha-recaptchav3',
		icon: { src: iconReCaptcha },
		title: __( 'reCAPTCHA v3', 'omniform' ),
		attributes: { service: 'recaptchav3', size: 'invisible' },
	},
	{
		name: 'captcha-hcaptcha',
		icon: { src: iconHCaptcha },
		title: __( 'hCaptcha', 'omniform' ),
		attributes: { service: 'hcaptcha', size: 'normal' },
	},
	{
		name: 'captcha-turnstile',
		icon: { src: iconTurnstile },
		title: __( 'Turnstile', 'omniform' ),
		attributes: { service: 'turnstile', size: 'normal' },
	},
];

variations.forEach( ( variation ) => {
	variation.isActive = ( blockAttributes, variationAttributes ) =>
		blockAttributes.service ===
		variationAttributes.service;

	if ( ! variation.scope ) {
		variation.scope = [ 'inserter', 'block', 'transform' ];
	}
} );

export default variations;
