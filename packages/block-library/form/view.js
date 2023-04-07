/**
 * WordPress dependencies
 */
import apiFetch from '@wordpress/api-fetch';

( function() {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function() {
		const formResponseHandler = ( event ) => {
			event.preventDefault();

			const formElement = event.target;
			const { action: url, method } = formElement;
			const body = new FormData( formElement );

			const debug = { url, method, data: {} };
			for ( const pair of body.entries() ) {
				debug.data[ pair[ 0 ] ] = pair[ 1 ];
			}
			console.debug( 'debug', debug );

			apiFetch( {
				url,
				method,
				body,
			} ).then( ( response ) => {
				document.getElementsByClassName( 'omniform-response-message' )[ 0 ].innerText = 'Thank you for signing up!';
				console.debug( 'response', response );
			} ).catch( ( error ) => {
				document.getElementsByClassName( 'omniform-response-message' )[ 0 ].innerText = 'Something went wrong...';
				console.debug( 'error', error );
			} );
		};

		document.querySelectorAll( 'form.wp-block-omniform-form' )
			.forEach( ( form ) => form.addEventListener( 'submit', formResponseHandler ) );
	} );
}() );
