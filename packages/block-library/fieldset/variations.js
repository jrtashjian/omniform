/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

const variations = [
	{
		name: 'fieldset-radios',
		title: __( 'Multiple Choice', 'omniform' ),
		description: __( '', 'omnigroup' ),
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
	{
		name: 'fieldset-name',
		title: __( 'Full Name', 'omniform' ),
		description: __( '', 'omnigroup' ),
		attributes: {
			fieldLabel: __( 'Full Name', 'omniform' ),
			fieldName: 'full-name',
		},
		innerBlocks: [
			{
				attributes: {
					fieldLabel: __( 'Title', 'omniform' ),
					fieldName: 'title',
				},
				innerBlocks: [
					[ 'omniform/select-option', { fieldLabel: __( 'Mr.', 'omniform' ), fieldName: 'mr.' } ],
					[ 'omniform/select-option', { fieldLabel: __( 'Mrs.', 'omniform' ), fieldName: 'mrs.' } ],
					[ 'omniform/select-option', { fieldLabel: __( 'Miss', 'omniform' ), fieldName: 'miss' } ],
					[ 'omniform/select-option', { fieldLabel: __( 'Ms.', 'omniform' ), fieldName: 'ms.' } ],
					[ 'omniform/select-option', { fieldLabel: __( 'Dr.', 'omniform' ), fieldName: 'dr.' } ],
					[ 'omniform/select-option', { fieldLabel: __( 'Prof.', 'omniform' ), fieldName: 'prof.' } ],
					[ 'omniform/select-option', { fieldLabel: __( 'Rev.', 'omniform' ), fieldName: 'rev.' } ],
					[ 'omniform/select-option', { fieldLabel: __( 'Hon.', 'omniform' ), fieldName: 'hon.' } ],
				],
				name: 'omniform/field-select',
			},
			{
				attributes: {
					fieldType: 'text',
					fieldLabel: __( 'First Name', 'omniform' ),
					fieldName: 'first-name',
				},
				name: 'omniform/field-input',
			},
			{
				attributes: {
					fieldType: 'text',
					fieldLabel: __( 'Last Name', 'omniform' ),
					fieldName: 'last-name',
				},
				name: 'omniform/field-input',
			},
		],
	},
];

export default variations;
