/* global hcaptcha, grecaptcha */

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	InspectorControls,
} from '@wordpress/block-editor';
import {
	Disabled,
	PanelBody,
	TextControl,
} from '@wordpress/components';
import { useEffect, useRef } from '@wordpress/element';
import { useEntityProp } from '@wordpress/core-data';

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
	const scriptFound = scripts.find( ( found ) => found.type === id );
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

	useEffect( () => {
		if ( ! siteKey ) {
			return;
		}

		const wrapper = document.createElement( 'div' );

		mountCaptchaScript( serviceSlug, serviceUrls[ serviceSlug ] )
			.then( () => {
				( 'hcaptcha' === service ? hcaptcha : grecaptcha ).render( wrapper, {
					sitekey: siteKey,
					theme,
					size,
				} );
			} )
			.catch( ( error ) => {
				console.debug( { error } );
			} );

		container.current.appendChild( wrapper );

		return () => {
			wrapper.remove();
		};
	}, [ siteKey, service, theme ] );

	const blockProps = useBlockProps();

	return (
		<>
			<div { ...blockProps }>
				<Disabled>
					<div ref={ container } />
				</Disabled>
			</div>
			<InspectorControls>
				<PanelBody title={ __( 'Settings', 'omniform' ) }>

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

				</PanelBody>
			</InspectorControls>
		</>
	);
};
export default Edit;
