/**
 * WordPress dependencies
 */
import { addFilter } from '@wordpress/hooks';

let captchaWidgetId = null;

// Captcha widget is loaded asynchronously, so we need to wait for it to be ready.
window.omniformCaptchaOnLoad = () => {
	const target = document.querySelector( '.wp-block-omniform-captcha' );

	const params = {
		sitekey: target.dataset.sitekey,
		theme: target.dataset.theme,
		size: target.dataset.size,
	};

	switch ( target.dataset.service ) {
		case 'hcaptcha':
			captchaWidgetId = window.hcaptcha.render( target, params );
			break;
		case 'recaptchav2':
			captchaWidgetId = window.grecaptcha.render( target, params );
			break;
	}
};

// Add a filter to validate the captcha.
addFilter( 'omniform.prepareFormElementForSubmission', 'omniform/captcha/validate', async ( formElement ) => {
	const target = formElement.querySelector( '.wp-block-omniform-captcha' );
	const captchaResponse = formElement.querySelector( '[name$="captcha-response"]' );

	// reCAPTCHA v3
	if ( 'recaptchav3' === target.dataset.service ) {
		await window.grecaptcha.execute( target.dataset.sitekey, { action: 'submit' } ).then( ( token ) => {
			captchaResponse.value = token;
		} );
	}

	// reCAPTCHA v2
	if ( 'recaptchav2' === target.dataset.service ) {
		if ( 'invisible' === target.dataset.size ) {
			await window.grecaptcha.execute().then( ( token ) => {
				captchaResponse.value = token;
				return token;
			} );
		} else {
			await window.grecaptcha.getResponse( captchaWidgetId );
		}
	}

	// hCaptcha
	if ( 'hcaptcha' === target.dataset.service ) {
		await window.hcaptcha.execute( captchaWidgetId, { async: true } ).then( ( { response: token } ) => {
			captchaResponse.value = token;
		} );
	}

	return formElement;
} );
