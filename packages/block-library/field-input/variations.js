/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

const variations = [
	{
		name: 'field-email',
		title: __( 'field-email', 'inquirywp' ),
		attributes: { type: 'email' },
	},
	{
		name: 'field-url',
		title: __( 'field-url', 'inquirywp' ),
		attributes: { type: 'url' },
	},
	{
		name: 'field-number',
		title: __( 'field-number', 'inquirywp' ),
		attributes: { type: 'number' },
	},
	{
		name: 'field-checkbox',
		title: __( 'field-checkbox', 'inquirywp' ),
		attributes: { type: 'checkbox' },
	},
	{
		name: 'field-radio',
		title: __( 'field-radio', 'inquirywp' ),
		attributes: { type: 'radio' },
	},
];

variations.forEach( ( variation ) => {
	variation.isActive = ( blockAttributes, variationAttributes ) =>
		blockAttributes.type ===
	variationAttributes.type;
} );

export default variations;
