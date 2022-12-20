/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

const variations = [
	{
		// This is a default to improve variation transforms.
		name: 'field-select-single',
		title: __( 'Select', 'omniform' ),
		description: __( 'A field with multiple options where a single choice can be made.', 'omnigroup' ),
		attributes: { isMultiple: false },
		scope: [ 'transform' ],
	},
	{
		name: 'field-select-multiple',
		title: __( 'Select Multiple', 'omniform' ),
		description: __( 'A field with multiple options where multiple choices can be made.', 'omnigroup' ),
		attributes: { isMultiple: true, fieldPlaceholder: '' },
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
