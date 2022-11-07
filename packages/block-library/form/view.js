/* global FormData */
( function() {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function() {
		const formSubmissionHandler = ( event ) => {
			event.preventDefault();

			const formElement = event.target;
			const { action, method } = formElement;
			const body = new FormData( formElement );

			console.debug( { action, method, body } );

			// fetch( action, { method, body } )
			// 	.then( ( response ) => console.debug( response ) )
			// 	.catch( ( error ) => console.debug( error ) );
		};

		// document.querySelectorAll( 'form.wp-block-inquirywp-form' )
		// 	.forEach( ( form ) => form.addEventListener( 'submit', formSubmissionHandler ) );
	} );
}() );
