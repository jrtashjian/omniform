/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	fieldCheckbox,
	fieldDate,
	fieldEmail,
	fieldInput,
	fieldNumber,
	fieldRadio,
	fieldTel,
	fieldTime,
	fieldUrl,
	fieldTextarea,
	fieldSelect,
} from '../shared/icons';

/**
 * An example of a text input field.
 *
 * @param {string} fieldType  The type of input field.
 * @param {string} fieldLabel The label for the input field.
 * @return {Object} The example.
 */
const inputTextExample = ( fieldType, fieldLabel ) => ( {
	attributes: {
		fieldLabel: fieldLabel || __( 'Field Label', 'omniform' ),
	},
	innerBlocks: [
		{
			name: 'omniform/label',
		},
		{
			name: 'omniform/input',
			attributes: {
				fieldType,
			},
		},
	],
} );

/**
 * An example of a checkbox or radio input field.
 *
 * @param {string} fieldType  The type of input field.
 * @param {string} fieldLabel The label for the input field.
 * @return {Object} The example.
 */
const inputOptionExample = ( fieldType, fieldLabel ) => ( {
	attributes: {
		fieldLabel: fieldLabel || __( 'Field Label', 'omniform' ),
		layout: { type: 'flex', orientation: 'horizontal', justifyContent: 'left' },
	},
	innerBlocks: [
		{
			name: 'omniform/input',
			attributes: {
				fieldType,
			},
		},
		{
			name: 'omniform/label',
		},
	],
} );

const variations = [
	{
		name: 'field-text',
		icon: { src: fieldInput },
		title: __( 'Text', 'omniform' ),
		description: __( 'A field for brief responses.', 'omniform' ),
		example: inputTextExample( 'text', __( 'Short Text', 'omniform' ) ),
		innerBlocks: inputTextExample( 'text' ).innerBlocks,
		isDefault: true,
	},
	{
		name: 'field-email',
		icon: { src: fieldEmail },
		title: __( 'Email', 'omniform' ),
		description: __( 'A field for collecting an email address.', 'omniform' ),
		example: inputTextExample( 'email', __( 'Email Address', 'omniform' ) ),
		innerBlocks: inputTextExample( 'email' ).innerBlocks,
	},
	{
		name: 'field-url',
		icon: { src: fieldUrl },
		title: __( 'URL', 'omniform' ),
		description: __( 'A field for collecting a website address or URL.', 'omniform' ),
		example: inputTextExample( 'url', __( 'URL', 'omniform' ) ),
		innerBlocks: inputTextExample( 'url' ).innerBlocks,
	},
	{
		name: 'field-number',
		icon: { src: fieldNumber },
		title: __( 'Number', 'omniform' ),
		description: __( 'A field for collecting a numerical value.', 'omniform' ),
		example: inputTextExample( 'number', __( 'Number', 'omniform' ) ),
		innerBlocks: inputTextExample( 'number' ).innerBlocks,
	},
	{
		name: 'field-checkbox',
		icon: { src: fieldCheckbox },
		title: __( 'Checkbox', 'omniform' ),
		description: __( 'A field that allows for multiple options and choices.', 'omniform' ),
		attributes: {
			layout: { type: 'flex', orientation: 'horizontal', justifyContent: 'left' },
		},
		example: inputOptionExample( 'checkbox', __( 'Checkbox', 'omniform' ) ),
		innerBlocks: inputOptionExample( 'checkbox' ).innerBlocks,
	},
	{
		name: 'field-radio',
		icon: { src: fieldRadio },
		title: __( 'Radio', 'omniform' ),
		description: __( 'A field that can be grouped, from which a single choice can be made.', 'omniform' ),
		attributes: {
			layout: { type: 'flex', orientation: 'horizontal', justifyContent: 'left' },
		},
		example: inputOptionExample( 'radio', __( 'Radio', 'omniform' ) ),
		innerBlocks: inputOptionExample( 'radio' ).innerBlocks,
	},
	{
		name: 'field-date',
		icon: { src: fieldDate },
		title: __( 'Date', 'omniform' ),
		description: __( 'A field for collecting a formatted date.', 'omniform' ),
		example: inputTextExample( 'date', __( 'Date', 'omniform' ) ),
		innerBlocks: inputTextExample( 'date' ).innerBlocks,
	},
	{
		name: 'field-datetime-local',
		icon: { src: fieldDate },
		title: __( 'Datetime', 'omniform' ),
		description: __( 'A field for collecting a localized date and time.', 'omniform' ),
		example: inputTextExample( 'datetime-local', __( 'Date', 'omniform' ) ),
		innerBlocks: inputTextExample( 'datetime-local' ).innerBlocks,
	},
	{
		name: 'field-month',
		icon: { src: fieldDate },
		title: __( 'Month', 'omniform' ),
		description: __( 'A field for collecting a month formatted date.', 'omniform' ),
		example: inputTextExample( 'month', __( 'Month', 'omniform' ) ),
		innerBlocks: inputTextExample( 'month' ).innerBlocks,
	},
	{
		name: 'field-tel',
		icon: { src: fieldTel },
		title: __( 'Phone', 'omniform' ),
		description: __( 'A field for collecting a telephone number.', 'omniform' ),
		example: inputTextExample( 'tel', __( 'Phone Number', 'omniform' ) ),
		innerBlocks: inputTextExample( 'tel' ).innerBlocks,
	},
	{
		name: 'field-time',
		icon: { src: fieldTime },
		title: __( 'Time', 'omniform' ),
		description: __( 'A field for collecting a formatted time.', 'omniform' ),
		example: inputTextExample( 'time', __( 'Time', 'omniform' ) ),
		innerBlocks: inputTextExample( 'time' ).innerBlocks,
	},
	{
		name: 'field-week',
		icon: { src: fieldDate },
		title: __( 'Week', 'omniform' ),
		description: __( 'A field for collecting a week formatted date.', 'omniform' ),
		example: inputTextExample( 'week', __( 'Week', 'omniform' ) ),
		innerBlocks: inputTextExample( 'week' ).innerBlocks,
	},
	{
		name: 'field-textarea',
		icon: { src: fieldTextarea },
		title: __( 'Textarea', 'omniform' ),
		description: __( 'A field for collecting a week formatted date.', 'omniform' ),
		example: {
			attributes: {
				fieldLabel: __( 'Long Text', 'omniform' ),
			},
			innerBlocks: [
				{ name: 'omniform/label' },
				{
					name: 'omniform/textarea',
					attributes: { style: { dimensions: { minHeight: '230px' } } },
				},
			],
		},
		innerBlocks: [
			{ name: 'omniform/label' },
			{
				name: 'omniform/textarea',
				attributes: { style: { dimensions: { minHeight: '230px' } } },
			},
		],
	},
	{
		name: 'field-select',
		icon: { src: fieldSelect },
		title: __( 'Select', 'omniform' ),
		description: __( 'A field with multiple options where a single choice can be made.', 'omniform' ),
		example: {
			attributes: {
				fieldLabel: __( 'Single Select', 'omniform' ),
			},
			innerBlocks: [
				{ name: 'omniform/label' },
				{ name: 'omniform/select' },
			],
		},
		innerBlocks: [
			{ name: 'omniform/label' },
			{ name: 'omniform/select' },
		],
	},
	{
		name: 'field-select-multiple',
		icon: { src: fieldSelect },
		title: __( 'Select Multiple', 'omniform' ),
		description: __( 'A field with multiple options where multiple choices can be made.', 'omniform' ),
		example: {
			attributes: {
				fieldLabel: __( 'Multi-Select', 'omniform' ),
			},
			innerBlocks: [
				{ name: 'omniform/label' },
				{
					name: 'omniform/select',
					attributes: { isMultiple: true, style: { dimensions: { minHeight: '130px' } } },
					innerBlocks: [
						{ name: 'omniform/select-option', attributes: { fieldLabel: __( 'Option One', 'omniform' ) } },
						{ name: 'omniform/select-option', attributes: { fieldLabel: __( 'Option Two', 'omniform' ) } },
						{ name: 'omniform/select-option', attributes: { fieldLabel: __( 'Option Three', 'omniform' ) } },
					],
				},
			],
		},
		innerBlocks: [
			{ name: 'omniform/label' },
			{
				name: 'omniform/select',
				attributes: { isMultiple: true, style: { dimensions: { minHeight: '230px' } } },
			},
		],
	},
];

export default variations;
