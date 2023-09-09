/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

const variations = [
	{
		name: 'fieldset-radios',
		title: __( 'Multiple Choice', 'omniform' ),
		description: __( 'A fieldset containing multiple radio fields for exclusive choices.', 'omniform' ),
		attributes: {
			fieldLabel: __( 'Multiple Choice', 'omniform' ),
			fieldName: 'multiple-choice',
		},
		innerBlocks: [
			{
				attributes: {
					fieldType: 'radio',
					fieldLabel: __( 'Option One', 'omniform' ),
					fieldName: 'option-one',
				},
				name: 'omniform/field-input',
			},
			{
				attributes: {
					fieldType: 'radio',
					fieldLabel: __( 'Option Two', 'omniform' ),
					fieldName: 'option-two',
				},
				name: 'omniform/field-input',
			},
			{
				attributes: {
					fieldType: 'radio',
					fieldLabel: __( 'Option Three', 'omniform' ),
					fieldName: 'option-three',
				},
				name: 'omniform/field-input',
			},
		],
	},
];

export default variations;
