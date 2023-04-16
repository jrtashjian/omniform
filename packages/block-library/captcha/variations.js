/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { Path, SVG } from '@wordpress/primitives';

const iconReCaptcha = (
	<SVG viewBox="0 0 24 24">
		<Path fill="#1C3AA9" d="M22 11.986c0-.143-.004-.286-.01-.43V3.44l-2.237 2.244A9.956 9.956 0 0 0 12.015 2 9.958 9.958 0 0 0 4.06 5.962l3.667 3.717a4.868 4.868 0 0 1 1.486-1.672c.64-.501 1.547-.91 2.802-.91a1 1 0 0 1 .355.05 4.833 4.833 0 0 1 3.696 2.233l-2.596 2.604c3.288-.013 7.003-.02 8.53.002Z" />
		<Path fill="#4285F4" d="M11.957 2c-.143 0-.286.004-.428.01H3.436l2.237 2.244A10.007 10.007 0 0 0 2 12.014c0 3.257 1.55 6.15 3.95 7.979l3.706-3.678a4.864 4.864 0 0 1-1.667-1.49c-.5-.642-.908-1.552-.908-2.81 0-.153.018-.27.051-.356A4.851 4.851 0 0 1 9.36 7.952l2.596 2.603c-.013-3.298-.02-7.023.002-8.555Z" />
		<Path fill="#ABABAB" d="M2 12.014c0 .144.004.287.01.43v8.116l2.238-2.244A9.956 9.956 0 0 0 11.985 22a9.959 9.959 0 0 0 7.956-3.962l-3.668-3.717a4.869 4.869 0 0 1-1.485 1.672c-.64.501-1.548.911-2.803.911a.998.998 0 0 1-.354-.051 4.833 4.833 0 0 1-3.696-2.233l2.596-2.604c-3.289.013-7.004.02-8.531-.002Z" />
	</SVG>
);

const iconHCaptcha = (
	<SVG viewBox="0 0 24 24">
		<Path fill="#0074BF" d="M17 19.5h-2.5V22H17v-2.5Z" opacity=".5" />
		<Path fill="#0074BF" d="M14.5 19.5H12V22h2.5v-2.5ZM12 19.5H9.5V22H12v-2.5Z" opacity=".7" />
		<Path fill="#0074BF" d="M9.5 19.5H7V22h2.5v-2.5Z" opacity=".5" />
		<Path fill="#0082BF" d="M19.5 17H17v2.5h2.5V17Z" opacity=".7" />
		<Path fill="#0082BF" d="M17 17h-2.5v2.5H17V17Z" opacity=".8" />
		<Path fill="#0082BF" d="M14.5 17H12v2.5h2.5V17ZM12 17H9.5v2.5H12V17Z" />
		<Path fill="#0082BF" d="M9.5 17H7v2.5h2.5V17Z" opacity=".8" />
		<Path fill="#0082BF" d="M7 17H4.5v2.5H7V17Z" opacity=".7" />
		<Path fill="#008FBF" d="M22 14.5h-2.5V17H22v-2.5Z" opacity=".5" />
		<Path fill="#008FBF" d="M19.5 14.5H17V17h2.5v-2.5Z" opacity=".8" />
		<Path fill="#008FBF" d="M17 14.5h-2.5V17H17v-2.5ZM14.5 14.5H12V17h2.5v-2.5ZM12 14.5H9.5V17H12v-2.5ZM9.5 14.5H7V17h2.5v-2.5Z" />
		<Path fill="#008FBF" d="M7 14.5H4.5V17H7v-2.5Z" opacity=".8" />
		<Path fill="#008FBF" d="M4.5 14.5H2V17h2.5v-2.5Z" opacity=".5" />
		<Path fill="#009DBF" d="M22 12h-2.5v2.5H22V12Z" opacity=".7" />
		<Path fill="#009DBF" d="M19.5 12H17v2.5h2.5V12ZM17 12h-2.5v2.5H17V12ZM14.5 12H12v2.5h2.5V12ZM12 12H9.5v2.5H12V12ZM9.5 12H7v2.5h2.5V12ZM7 12H4.5v2.5H7V12Z" />
		<Path fill="#009DBF" d="M4.5 12H2v2.5h2.5V12Z" opacity=".7" />
		<Path fill="#00ABBF" d="M22 9.5h-2.5V12H22V9.5Z" opacity=".7" />
		<Path fill="#00ABBF" d="M19.5 9.5H17V12h2.5V9.5ZM17 9.5h-2.5V12H17V9.5ZM14.5 9.5H12V12h2.5V9.5ZM12 9.5H9.5V12H12V9.5ZM9.5 9.5H7V12h2.5V9.5ZM7 9.5H4.5V12H7V9.5Z" />
		<Path fill="#00ABBF" d="M4.5 9.5H2V12h2.5V9.5Z" opacity=".7" />
		<Path fill="#00B9BF" d="M22 7h-2.5v2.5H22V7Z" opacity=".5" />
		<Path fill="#00B9BF" d="M19.5 7H17v2.5h2.5V7Z" opacity=".8" />
		<Path fill="#00B9BF" d="M17 7h-2.5v2.5H17V7ZM14.5 7H12v2.5h2.5V7ZM12 7H9.5v2.5H12V7ZM9.5 7H7v2.5h2.5V7Z" />
		<Path fill="#00B9BF" d="M7 7H4.5v2.5H7V7Z" opacity=".8" />
		<Path fill="#00B9BF" d="M4.5 7H2v2.5h2.5V7Z" opacity=".5" />
		<Path fill="#00C6BF" d="M19.5 4.5H17V7h2.5V4.5Z" opacity=".7" />
		<Path fill="#00C6BF" d="M17 4.5h-2.5V7H17V4.5Z" opacity=".8" />
		<Path fill="#00C6BF" d="M14.5 4.5H12V7h2.5V4.5ZM12 4.5H9.5V7H12V4.5Z" />
		<Path fill="#00C6BF" d="M9.5 4.5H7V7h2.5V4.5Z" opacity=".8" />
		<Path fill="#00C6BF" d="M7 4.5H4.5V7H7V4.5Z" opacity=".7" />
		<Path fill="#00D4BF" d="M17 2h-2.5v2.5H17V2Z" opacity=".5" />
		<Path fill="#00D4BF" d="M14.5 2H12v2.5h2.5V2ZM12 2H9.5v2.5H12V2Z" opacity=".7" />
		<Path fill="#00D4BF" d="M9.5 2H7v2.5h2.5V2Z" opacity=".5" />
		<Path fill="#fff" d="m8.582 11.211.697-1.559c.254-.4.22-.89-.058-1.167a.713.713 0 0 0-.435-.206.77.77 0 0 0-.33.033 1.05 1.05 0 0 0-.58.45s-.954 2.224-1.31 3.224c-.354 1-.212 2.834 1.156 4.206 1.451 1.45 3.553 1.783 4.893.777a.84.84 0 0 0 .157-.103l4.13-3.448c.2-.166.497-.508.23-.898-.26-.38-.752-.121-.953.007l-2.376 1.729a.107.107 0 0 1-.154-.018c-.06-.074-.071-.27.024-.349l3.643-3.092c.315-.284.359-.696.104-.978-.249-.277-.644-.268-.962.018l-3.28 2.564a.145.145 0 0 1-.21-.027c-.065-.072-.09-.197-.016-.27L16.667 8.5a.726.726 0 0 0 .036-1.026.693.693 0 0 0-.504-.216.74.74 0 0 0-.522.207l-3.796 3.566c-.09.09-.269 0-.29-.106a.114.114 0 0 1 .032-.106L14.53 7.51a.715.715 0 1 0-1.01-1.014l-4.407 4.872c-.158.158-.39.166-.501.074a.166.166 0 0 1-.029-.23Z" />
	</SVG>
);

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
