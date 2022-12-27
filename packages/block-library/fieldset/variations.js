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
		},
		innerBlocks: [
			{
				attributes: {
					fieldType: 'radio',
					fieldLabel: __( 'Option One', 'omniform' ),
				},
				name: 'omniform/field-input',
			},
			{
				attributes: {
					fieldType: 'radio',
					fieldLabel: __( 'Option Two', 'omniform' ),
				},
				name: 'omniform/field-input',
			},
			{
				attributes: {
					fieldType: 'radio',
					fieldLabel: __( 'Option Three', 'omniform' ),
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
		},
		innerBlocks: [
			{
				attributes: {
					fieldLabel: __( 'Title', 'omniform' ),
				},
				innerBlocks: [
					[ 'omniform/select-option', { fieldLabel: __( 'Mr.', 'omniform' ) } ],
					[ 'omniform/select-option', { fieldLabel: __( 'Mrs.', 'omniform' ) } ],
					[ 'omniform/select-option', { fieldLabel: __( 'Miss', 'omniform' ) } ],
					[ 'omniform/select-option', { fieldLabel: __( 'Ms.', 'omniform' ) } ],
					[ 'omniform/select-option', { fieldLabel: __( 'Dr.', 'omniform' ) } ],
					[ 'omniform/select-option', { fieldLabel: __( 'Prof.', 'omniform' ) } ],
					[ 'omniform/select-option', { fieldLabel: __( 'Rev.', 'omniform' ) } ],
					[ 'omniform/select-option', { fieldLabel: __( 'Hon.', 'omniform' ) } ],
				],
				name: 'omniform/field-select',
			},
			{
				attributes: {
					fieldType: 'text',
					fieldLabel: __( 'First Name', 'omniform' ),
				},
				name: 'omniform/field-input',
			},
			{
				attributes: {
					fieldType: 'text',
					fieldLabel: __( 'Last Name', 'omniform' ),
				},
				name: 'omniform/field-input',
			},
		],
	},
];

export default variations;
