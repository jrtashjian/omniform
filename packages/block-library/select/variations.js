/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

const variations = [
	{
		// This is a default to improve variation transforms.
		name: 'select-single',
		title: __( 'Select', 'omniform' ),
		description: __( 'A field with multiple options where a single choice can be made.', 'omniform' ),
		attributes: { isMultiple: false },
		scope: [ 'transform' ],
	},
	{
		name: 'select-multiple',
		title: __( 'Select Multiple', 'omniform' ),
		description: __( 'A field with multiple options where multiple choices can be made.', 'omniform' ),
		attributes: { isMultiple: true, fieldPlaceholder: undefined },
	},
];

variations.forEach( ( variation ) => {
	variation.isActive = ( blockAttributes, variationAttributes ) =>
		blockAttributes.isMultiple ===
		variationAttributes.isMultiple;

	if ( ! variation.scope ) {
		variation.scope = [ 'inserter', 'block', 'transform' ];
	}
} );

export default variations;
