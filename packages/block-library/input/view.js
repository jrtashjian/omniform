( function() {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function() {
		// For range inputs, update the label with the current value.
		document.querySelectorAll( '.wp-block-omniform-input[type="range"]' ).forEach( ( input ) => {
			input.addEventListener( 'input', ( event ) => {
				const field = event.target.closest( '.wp-block-omniform-field' );
				const label = field?.querySelector( '.wp-block-omniform-label' );

				if ( label ) {
					label.setAttribute( 'data-range-value', event.target.value );
				}
			} );
		} );
	} );
}() );
