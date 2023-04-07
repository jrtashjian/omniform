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

			const messageContainer = event.target.querySelector( '.omniform-response-container' );

			apiFetch( {
				url,
				method,
				body,
			} ).then( () => {
				messageContainer.innerHTML = '';
				messageContainer.append( createParagraph( 'Thank you for signing up!' ) );

				// Reset the form.
				event.target.reset();
			} ).catch( ( error ) => {
				messageContainer.innerHTML = '';
				messageContainer.append( createParagraph( 'Something went wrong...' ) );
				messageContainer.append( createUnorderedList( Object.values( error.invalid_fields ) ) );
			} );
		};

		// Create a paragraph element from text.
		const createParagraph = ( text ) => {
			const paragraph = document.createElement( 'p' );
			paragraph.textContent = text;
			return paragraph;
		};

		// Create an unordered list from an array of items.
		const createUnorderedList = ( list ) => {
			const listElement = document.createElement( 'ul' );

			list.forEach( ( item ) => {
				const listItem = document.createElement( 'li' );
				listItem.textContent = item;
				listElement.appendChild( listItem );
			} );

			return listElement;
		};

		// Add event listeners to all omniforms.
		document.querySelectorAll( 'form.wp-block-omniform-form' )
			.forEach( ( form ) => form.addEventListener( 'submit', formResponseHandler ) );
	} );
}() );
