/**
 * WordPress dependencies
 */
import apiFetch from '@wordpress/api-fetch';
import { applyFilters } from '@wordpress/hooks';

( function() {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function() {
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
		document.querySelectorAll( 'form.wp-block-omniform-form' ).forEach( ( form ) => {
			// Only add event listeners to OmniForms.
			if ( ! form.action.includes( '/omniform/v1/forms/' ) ) {
				return;
			}

			const containersInitialState = {};

			const successContainer = form.querySelector( '.wp-block-omniform-response-notification.success-response-notification' );
			const errorContainer = form.querySelector( '.wp-block-omniform-response-notification.error-response-notification' );
			const containers = [ successContainer, errorContainer ];

			/**
			 * Save the initial state of the containers.
			 *
			 * @param {HTMLElement} container
			 */
			const saveInitialState = ( container ) => {
				containersInitialState[ container.className ] = Array.from( container.children ).map( ( child ) => child.cloneNode( true ) );
				container.textContent = '';
			};

			/**
			 * Show a message in a container.
			 *
			 * @param {HTMLElement} container
			 * @param {Array}       additionalChildren
			 */
			const showMessage = ( container, additionalChildren = [] ) => {
				// Hide all containers.
				containers.forEach( ( elm ) => {
					elm.textContent = '';
					elm.style.display = 'none';
				} );

				// Reset the container to its initial state.
				container.textContent = '';
				containersInitialState[ container.className ].forEach( ( child ) => {
					container.appendChild( child.cloneNode( true ) );
				} );

				// Add additional children to the container.
				additionalChildren.forEach( ( child ) => {
					container.appendChild( child );
				} );

				// Show the message container.
				container.style.display = 'block';

				// Focus the message container.
				container.setAttribute( 'tabindex', '-1' );
				container.focus();
				container.removeAttribute( 'tabindex' );
			};

			// Save initial state and clear content for both containers
			containers.forEach( ( container ) => saveInitialState( container ) );

			const formResponseHandler = async ( event ) => {
				event.preventDefault();

				// Allow plugins to hook into the form submission.
				const formElement = await applyFilters( 'omniform.prepareFormElementForSubmission', event.target );

				const { action: url, method } = formElement;
				const body = new FormData( formElement );

				await apiFetch( {
					url,
					method,
					body,
				} ).then( () => {
					showMessage( successContainer );

					// Reset the form.
					event.target.reset();
				} ).catch( ( error ) => {
					showMessage(
						errorContainer,
						[ createUnorderedList( Object.values( error.invalid_fields ) ) ]
					);
				} );
			};

			form.addEventListener( 'submit', formResponseHandler );
		} );
	} );
}() );
