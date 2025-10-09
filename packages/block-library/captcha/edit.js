/* global omniform */

/**
 * WordPress dependencies
 */
import { __, sprintf } from '@wordpress/i18n';
import {
	useBlockProps,
	InspectorControls,
} from '@wordpress/block-editor';
import {
	Icon,
	PanelBody,
	TextControl,
	__experimentalToggleGroupControl as ToggleGroupControl,
	__experimentalToggleGroupControlOption as ToggleGroupControlOption,
	ExternalLink,
} from '@wordpress/components';
import { useEntityProp } from '@wordpress/core-data';

/**
 * Internal dependencies
 */
import {
	iconReCaptcha,
	iconHCaptcha,
	iconTurnstile,
} from '../shared/icons';

const services = {
	hcaptcha: {
		icon: iconHCaptcha,
		label: __( 'hCaptcha', 'omniform' ),
		setupLink: 'https://dashboard.hcaptcha.com/sites/new',
	},
	recaptchav2: {
		icon: iconReCaptcha,
		label: __( 'reCAPTCHA v2', 'omniform' ),
		setupLink: 'https://www.google.com/recaptcha/admin/create',
	},
	recaptchav3: {
		icon: iconReCaptcha,
		label: __( 'reCAPTCHA v3', 'omniform' ),
		setupLink: 'https://www.google.com/recaptcha/admin/create',
	},
	turnstile: {
		icon: iconTurnstile,
		label: __( 'Turnstile', 'omniform' ),
		setupLink: 'https://dash.cloudflare.com/?to=:/account/turnstile',
	},
};

const Edit = ( {
	attributes: { service, theme, size },
	setAttributes,
} ) => {
	const [ siteKey, setSiteKey ] = useEntityProp( 'root', 'site', `omniform_${ service }_site_key` );
	const [ secretKey, setSecretKey ] = useEntityProp( 'root', 'site', `omniform_${ service }_secret_key` );

	const blockProps = useBlockProps();

	const SetupInstructions = () => {
		return (
			<>
				{ sprintf(
					/* translators: 1: captcha service name */
					__( 'To start using %s, you need to sign up for an API key pair for your site. The key pair consists of a site key and secret key.', 'omniform' ),
					services[ service ].label
				) }
				&nbsp;
				<ExternalLink href={ services[ service ].setupLink }>
					{ __( 'Generate keys', 'omniform' ) }
				</ExternalLink>
			</>
		);
	};

	return (
		<>
			<div { ...blockProps }>
				{ siteKey ? (
					<img
						src={ `${ omniform.assetsUrl }${ service }-${ size }-${ theme }.png` }
						alt={ `${ services[ service ].label } preview` }
						style={ { display: 'block' } }
					/>
				) : (
					<div className="wp-block-omniform-captcha-placeholder">
						<Icon icon={ services[ service ].icon } size={ 36 } />
						<div className="wp-block-omniform-captcha-placeholder__instructions">
							<SetupInstructions />
						</div>
					</div>
				) }
			</div>
			<InspectorControls>
				<PanelBody title={ __( 'Settings', 'omniform' ) }>

					<p><SetupInstructions /></p>

					<TextControl
						label={ __( 'Site Key', 'omniform' ) }
						value={ siteKey }
						onChange={ setSiteKey }
						type="password"
						__nextHasNoMarginBottom
						__next40pxDefaultSize
					/>

					<TextControl
						label={ __( 'Secret Key', 'omniform' ) }
						value={ secretKey }
						onChange={ setSecretKey }
						type="password"
						__nextHasNoMarginBottom
						__next40pxDefaultSize
					/>

					<ToggleGroupControl
						label={ __( 'Theme', 'omniform' ) }
						value={ theme }
						onChange={ ( value ) => setAttributes( { theme: value } ) }
						isBlock
					>
						<ToggleGroupControlOption value="light" label={ __( 'Light', 'omniform' ) } />
						<ToggleGroupControlOption value="dark" label={ __( 'Dark', 'omniform' ) } />
					</ToggleGroupControl>

					{ 'recaptchav3' !== service && (
						<ToggleGroupControl
							label={ __( 'Size', 'omniform' ) }
							value={ size }
							onChange={ ( value ) => setAttributes( { size: value } ) }
							isBlock
						>
							<ToggleGroupControlOption value="normal" label={ __( 'Normal', 'omniform' ) } />
							<ToggleGroupControlOption value="compact" label={ __( 'Compact', 'omniform' ) } />
						</ToggleGroupControl>
					) }

				</PanelBody>
			</InspectorControls>
		</>
	);
};
export default Edit;
