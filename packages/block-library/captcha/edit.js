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
import { iconReCaptcha, iconHCaptcha } from '../shared/icons';

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

		script.onerror = ( event ) => reject( 'script-error' );

		document.head.appendChild( script );
	} );

	scripts.push( { promise, id } );
	return promise;
};

const Edit = ( {
	attributes: { service, theme, size },
	setAttributes,
} ) => {
	const container = useRef();

	const serviceSlug = 'hcaptcha' === service ? 'hcaptcha' : 'recaptcha';
	const serviceUrls = {
		hcaptcha: 'https://js.hcaptcha.com/1/api.js',
		recaptcha: 'https://www.google.com/recaptcha/api.js',
	};

	const [ siteKey, setSiteKey ] = useEntityProp( 'root', 'site', `omniform_${ service }_site_key` );
	const [ secretKey, setSecretKey ] = useEntityProp( 'root', 'site', `omniform_${ service }_secret_key` );

	const [ isLoaded, setIsLoaded ] = useState( false );

	useEffect( () => {
		if ( ! siteKey ) {
			return;
		}

		const wrapper = document.createElement( 'div' );

		mountCaptchaScript( serviceSlug, serviceUrls[ serviceSlug ] )
			.then( () => {
				( 'hcaptcha' === service ? window.hcaptcha : window.grecaptcha ).render( wrapper, {
					sitekey: siteKey,
					theme,
					size,
					badge: 'inline',
				} );
				setIsLoaded( true );
			} )
			.catch( ( error ) => {
				console.error( { error } );
				setIsLoaded( false );
			} );

		container.current.appendChild( wrapper );

		return () => {
			wrapper.remove();
		};
	}, [ siteKey, service, theme, size ] );

	const blockProps = useBlockProps();

	const SetupInstructions = () => {
		/* translators: 1: captcha service name */
		let createDesc = __( 'To start using %s, you need to sign up for an API key pair for your site. The key pair consists of a site key and secret key.', 'omniform' );

		switch ( service ) {
			case 'hcaptcha':
				createDesc = sprintf( createDesc, __( 'hCaptcha', 'omniform' ) );
				break;
			case 'recaptchav2':
				createDesc = sprintf( createDesc, __( 'reCAPTCHA v2', 'omniform' ) );
				break;
			case 'recaptchav3':
				createDesc = sprintf( createDesc, __( 'reCAPTCHA v3', 'omniform' ) );
				break;
		}

		return (
			<>
				{ createDesc }
				&nbsp;
				<ExternalLink
					href={
						'hcaptcha' === service
							? 'https://dashboard.hcaptcha.com/sites/new'
							: 'https://www.google.com/recaptcha/admin/create'
					}
				>
					{ __( 'Generate keys', 'omniform' ) }
				</ExternalLink>
			</>
		);
	};

	return (
		<>
			<div { ...blockProps }>
				{ ! ( isLoaded || siteKey ) && (
					<div className="wp-block-omniform-captcha-placeholder">
						<Icon icon={ 'hcaptcha' === service ? iconHCaptcha : iconReCaptcha } size={ 36 } />
						<div className="wp-block-omniform-captcha-placeholder__instructions">
							<SetupInstructions />
						</div>
					</div>
				) }
				<Disabled>
					<div
						ref={ container }
						id={ 'hcaptcha' === service ? 'hcapcha' : 'recaptcha' }
						className={ 'hcaptcha' === service ? 'h-capcha' : 'g-recaptcha' }
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
					/>

					<TextControl
						label={ __( 'Secret Key', 'omniform' ) }
						value={ secretKey }
						onChange={ setSecretKey }
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
