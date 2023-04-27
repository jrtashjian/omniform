/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';
import { applyFilters } from '@wordpress/hooks';

( function() {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function() {
		const formResponseHandler = async ( event ) => {
			event.preventDefault();

			// Allow plugins to hook into the form submission.
			const formElement = await applyFilters( 'omniform.prepareFormElementForSubmission', event.target );

			const { action: url, method } = formElement;
			const body = new FormData( formElement );

			const messageContainer = event.target.querySelector( '.omniform-response-container' );

			await apiFetch( {
				url,
				method,
				body,
			} ).then( () => {
				messageContainer.innerHTML = '';
				messageContainer.append( createParagraph( __( 'Success! Your submission has been completed.', 'omniform' ) ) );

				// Show the success message.
				messageContainer.style.display = 'block';
				messageContainer.style.borderLeftColor = 'var(--wp--preset--color--vivid-green-cyan,#00d084)';

				// Reset the form.
				event.target.reset();
			} ).catch( ( error ) => {
				messageContainer.innerHTML = '';
				messageContainer.append( createParagraph( __( 'Unfortunately, your submission was not successful. Please ensure all fields are correctly filled out and try again.', 'omniform' ) ) );

				if ( error.invalid_fields ) {
					messageContainer.append( createUnorderedList( Object.values( error.invalid_fields ) ) );
				}

				// Show the error message.
				messageContainer.style.display = 'block';
				messageContainer.style.borderLeftColor = 'var(--wp--preset--color--vivid-red,#cf2e2e)';
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
