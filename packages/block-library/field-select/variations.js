/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

const variations = [
	{
		name: 'field-select-multiple',
		title: __( 'Select Multiple', 'omniform' ),
		description: __( 'A field with multiple options where multiple choices can be made.', 'omnigroup' ),
		attributes: { isMultiple: true },
	},
];

variations.forEach( ( variation ) => {
	variation.isActive = ( blockAttributes, variationAttributes ) =>
		blockAttributes.isMultiple ===
		variationAttributes.isMultiple;
} );

export default variations;
