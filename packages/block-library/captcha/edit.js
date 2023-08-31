/**
 * WordPress dependencies
 */
import { __, sprintf } from '@wordpress/i18n';
import {
	useBlockProps,
	InspectorControls,
} from '@wordpress/block-editor';
import {
	Disabled,
	Icon,
	PanelBody,
	TextControl,
	__experimentalToggleGroupControl as ToggleGroupControl,
	__experimentalToggleGroupControlOption as ToggleGroupControlOption,
	ExternalLink,
} from '@wordpress/components';
import { useEffect, useRef, useState } from '@wordpress/element';
import { useEntityProp } from '@wordpress/core-data';

/**
 * Internal dependencies
 */
import {
	iconReCaptcha,
	iconHCaptcha,
	iconTurnstile,
} from '../shared/icons';

const scripts = [];

/**
 * Mounts the captcha script if it doesn't exist.
 *
 * @param {string} id  The script id.
 * @param {string} src The script src.
 * @return {Promise} Promise that resolves when the script is loaded.
 */
const mountCaptchaScript = ( id, src ) => {
	const SCRIPT_ID = `${ id }-script`;
	const ONLOAD_FN_NAME = 'handleCaptchaOnLoad';

	// If the script is already loaded, return the promise.
	const scriptFound = scripts.find( ( found ) => found.id === id );
	if ( document.getElementById( SCRIPT_ID ) && scriptFound ) {
		return scriptFound.promise;
	}

	// If the script is not loaded, create a new promise.
	const promise = new Promise( ( resolve, reject ) => {
		window[ ONLOAD_FN_NAME ] = resolve;

		const script = document.createElement( 'script' );
		script.id = SCRIPT_ID;
		script.src = `${ src }?onload=${ ONLOAD_FN_NAME }&render=explicit`;

		script.async = true;

		script.onerror = ( event ) => reject( __( 'Failed to load script:', 'omniform' ) + ' ' + src ); // eslint-disable-line no-unused-vars

		document.head.appendChild( script );
	} );

	scripts.push( { promise, id } );
	return promise;
};

const services = {
	hcaptcha: {
		icon: iconHCaptcha,
		label: __( 'hCaptcha', 'omniform' ),
		scriptUrl: 'https://js.hcaptcha.com/1/api.js',
		setupLink: 'https://dashboard.hcaptcha.com/sites/new',
		elementId: 'hcaptcha',
		elementClassName: 'h-captcha',
		globalAccessor: 'hcaptcha',
	},
	recaptchav2: {
		icon: iconReCaptcha,
		label: __( 'reCAPTCHA v2', 'omniform' ),
		scriptUrl: 'https://www.google.com/recaptcha/api.js',
		setupLink: 'https://www.google.com/recaptcha/admin/create',
		elementId: 'recaptcha',
		elementClassName: 'g-recaptcha',
		globalAccessor: 'grecaptcha',
	},
	recaptchav3: {
		icon: iconReCaptcha,
		label: __( 'reCAPTCHA v3', 'omniform' ),
		scriptUrl: 'https://www.google.com/recaptcha/api.js',
		setupLink: 'https://www.google.com/recaptcha/admin/create',
		elementId: 'recaptcha',
		elementClassName: 'g-recaptcha',
		globalAccessor: 'grecaptcha',
	},
	turnstile: {
		icon: iconTurnstile,
		label: __( 'Turnstile', 'omniform' ),
		scriptUrl: 'https://challenges.cloudflare.com/turnstile/v0/api.js',
		setupLink: 'https://dash.cloudflare.com/?to=:/account/turnstile',
		elementId: 'turnstile',
		elementClassName: 'cf-turnstile',
		globalAccessor: 'turnstile',
	},
};

const Edit = ( {
	attributes: { service, theme, size },
	setAttributes,
} ) => {
	const container = useRef();

	const [ siteKey, setSiteKey ] = useEntityProp( 'root', 'site', `omniform_${ service }_site_key` );
	const [ secretKey, setSecretKey ] = useEntityProp( 'root', 'site', `omniform_${ service }_secret_key` );

	const [ isLoaded, setIsLoaded ] = useState( false );
	const [ errorMsg, setErrorMsg ] = useState( null );

	useEffect( () => {
		if ( ! siteKey ) {
			return;
		}

		const wrapper = document.createElement( 'div' );

		mountCaptchaScript( services[ service ].elementId, services[ service ].scriptUrl )
			.then( () => {
				window[ services[ service ].globalAccessor ].render( wrapper, {
					sitekey: siteKey,
					theme,
					size,
					badge: 'inline',
				} );
				setIsLoaded( true );
				setErrorMsg( null );
			} )
			.catch( ( error ) => {
				setIsLoaded( false );
				setErrorMsg( error.message );
			} );

		container.current.appendChild( wrapper );

		return () => {
			wrapper.remove();
		};
	}, [ siteKey, service, theme, size ] );

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
				{ ( errorMsg || ! ( isLoaded || siteKey ) ) && (
					<div className="wp-block-omniform-captcha-placeholder">
						<Icon icon={ services[ service ].icon } size={ 36 } />
						<div className="wp-block-omniform-captcha-placeholder__instructions">
							<SetupInstructions />
							<div style={ { color: 'var(--wp--preset--color--vivid-red, "#cf2e2e")' } }>{ errorMsg }</div>
						</div>
					</div>
				) }
				<Disabled>
					<div
						ref={ container }
						className={ services[ service ].elementClassName }
					/>
				</Disabled>
			</div>
			<InspectorControls>
				<PanelBody title={ __( 'Settings', 'omniform' ) }>

					<p><SetupInstructions /></p>

					<TextControl
						label={ __( 'Site Key', 'omniform' ) }
						value={ siteKey }
						onChange={ setSiteKey }
						type="password"
					/>

					<TextControl
						label={ __( 'Secret Key', 'omniform' ) }
						value={ secretKey }
						onChange={ setSecretKey }
						type="password"
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
