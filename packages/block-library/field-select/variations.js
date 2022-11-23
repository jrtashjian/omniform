/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

const variations = [
	{
		name: 'field-select-multiple',
		title: __( 'field-select-multiple', 'omniform' ),
		attributes: { multiple: true },
	},
];

variations.forEach( ( variation ) => {
	variation.isActive = ( blockAttributes, variationAttributes ) =>
		blockAttributes.type ===
	variationAttributes.type;
} );

export default variations;
