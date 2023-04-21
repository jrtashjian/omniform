/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { iconReCaptcha, iconHCaptcha } from '../shared/icons';

const variations = [
	{
		name: 'captcha-recaptchav2',
		icon: { src: iconReCaptcha },
		title: __( 'reCAPTCHA v2', 'omniform' ),
		description: __( 'Verify requests with a challenge', 'omniform' ),
		attributes: { service: 'recaptchav2' },
		isDefault: true,
	},
	{
		name: 'captcha-recaptchav3',
		icon: { src: iconReCaptcha },
		title: __( 'reCAPTCHA v3', 'omniform' ),
		description: __( 'Verify requests with a score', 'omniform' ),
		attributes: { service: 'recaptchav3', size: 'invisible' },
	},
	{
		name: 'captcha-hcaptcha',
		icon: { src: iconHCaptcha },
		title: __( 'hCaptcha', 'omniform' ),
		description: __( 'Verify requests with a challenge', 'omniform' ),
		attributes: { service: 'hcaptcha' },
	},
];

variations.forEach( ( variation ) => {
	variation.isActive = ( blockAttributes, variationAttributes ) =>
		blockAttributes.service ===
		variationAttributes.service;
} );

export default variations;
