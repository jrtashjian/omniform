/* global FormData */
import tinycolor from 'tinycolor2';
import apiFetch from '@wordpress/api-fetch';

( function() {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function() {
		const formSubmissionHandler = ( event ) => {
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
				console.debug( 'response', response );
			} ).catch( ( error ) => {
				console.debug( 'error', error );
			} );
		};

		// document.querySelectorAll( 'form.wp-block-omniform-form' )
		// 	.forEach( ( form ) => form.addEventListener( 'submit', formSubmissionHandler ) );

		const isDark = ( elm ) => {
			const context = document.defaultView.getComputedStyle( elm, null );
			return tinycolor( context.getPropertyValue( 'color' ) ).isDark();
		};

		document.querySelectorAll( 'body' ).forEach(
			( elm ) => ! isDark( elm ) && elm.classList.add( 'is-dark-theme' )
		);
	} );
}() );
