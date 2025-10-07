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
	fieldPassword,
	fieldRadio,
	fieldSearch,
	fieldSelect,
	fieldTel,
	fieldTextarea,
	fieldTime,
	fieldUrl,
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
		category: 'omniform-standard-fields',
		icon: { src: fieldInput },
		title: __( 'Text', 'omniform' ),
		description: __( 'A field for brief text entry.', 'omniform' ),
		keywords: [ 'input', 'short' ],
		example: inputTextExample( 'text', __( 'Short Text', 'omniform' ) ),
		innerBlocks: inputTextExample( 'text' ).innerBlocks,
		isDefault: true,
	},
	{
		name: 'field-email',
		category: 'omniform-standard-fields',
		icon: { src: fieldEmail },
		title: __( 'Email', 'omniform' ),
		description: __( 'A field for collecting an email address.', 'omniform' ),
		keywords: [ 'input', 'address', 'mail' ],
		example: inputTextExample( 'email', __( 'Email Address', 'omniform' ) ),
		innerBlocks: inputTextExample( 'email' ).innerBlocks,
	},
	{
		name: 'field-username-email',
		category: 'omniform-standard-fields',
		icon: { src: fieldInput },
		title: __( 'Username or Email', 'omniform' ),
		description: __( 'A field for collecting a username or email address.', 'omniform' ),
		keywords: [ 'input', 'username', 'email', 'login' ],
		example: inputTextExample( 'username-email', __( 'Username or Email', 'omniform' ) ),
		innerBlocks: inputTextExample( 'username-email' ).innerBlocks,
	},
	{
		name: 'field-url',
		category: 'omniform-standard-fields',
		icon: { src: fieldUrl },
		title: __( 'URL', 'omniform' ),
		description: __( 'A field for collecting a website address or URL.', 'omniform' ),
		keywords: [ 'input', 'link', 'website' ],
		example: inputTextExample( 'url', __( 'URL', 'omniform' ) ),
		innerBlocks: inputTextExample( 'url' ).innerBlocks,
	},
	{
		name: 'field-number',
		category: 'omniform-standard-fields',
		icon: { src: fieldNumber },
		title: __( 'Number', 'omniform' ),
		description: __( 'A field for collecting a numerical value.', 'omniform' ),
		keywords: [ 'input', 'numeric', 'integer', 'decimal' ],
		example: inputTextExample( 'number', __( 'Number', 'omniform' ) ),
		innerBlocks: inputTextExample( 'number' ).innerBlocks,
	},
	{
		name: 'field-search',
		category: 'omniform-standard-fields',
		icon: { src: fieldSearch },
		title: __( 'Search', 'omniform' ),
		description: __( 'Enter keywords to search for information.', 'omniform' ),
		keywords: [ 'input', 'search' ],
		example: inputTextExample( 'search', __( 'Search', 'omniform' ) ),
		innerBlocks: inputTextExample( 'search' ).innerBlocks,
	},
	{
		name: 'field-password',
		category: 'omniform-standard-fields',
		icon: { src: fieldPassword },
		title: __( 'Password', 'omniform' ),
		description: __( 'A field for secure password entry.', 'omniform' ),
		keywords: [ 'input', 'password' ],
		example: inputTextExample( 'password', __( 'Password', 'omniform' ) ),
		innerBlocks: inputTextExample( 'password' ).innerBlocks,
	},
	{
		name: 'field-checkbox',
		category: 'omniform-standard-fields',
		icon: { src: fieldCheckbox },
		title: __( 'Checkbox', 'omniform' ),
		description: __( 'A field for selecting multiple options or choices.', 'omniform' ),
		keywords: [ 'input', 'box', 'multiple', 'choice' ],
		attributes: {
			className: 'is-style-inline',
		},
		example: inputOptionExample( 'checkbox', __( 'Checkbox', 'omniform' ) ),
		innerBlocks: inputOptionExample( 'checkbox' ).innerBlocks,
	},
	{
		name: 'field-radio',
		category: 'omniform-standard-fields',
		icon: { src: fieldRadio },
		title: __( 'Radio', 'omniform' ),
		description: __( 'A field in a group allowing for one selected choice.', 'omniform' ),
		keywords: [ 'input', 'single', 'choice' ],
		attributes: {
			className: 'is-style-inline',
		},
		example: inputOptionExample( 'radio', __( 'Radio', 'omniform' ) ),
		innerBlocks: inputOptionExample( 'radio' ).innerBlocks,
	},
	{
		name: 'field-date',
		category: 'omniform-standard-fields',
		icon: { src: fieldDate },
		title: __( 'Date', 'omniform' ),
		description: __( 'A field for collecting a formatted date.', 'omniform' ),
		keywords: [ 'input', 'calendar', 'time' ],
		example: inputTextExample( 'date', __( 'Date', 'omniform' ) ),
		innerBlocks: inputTextExample( 'date' ).innerBlocks,
	},
	{
		name: 'field-datetime-local',
		category: 'omniform-standard-fields',
		icon: { src: fieldDate },
		title: __( 'Datetime', 'omniform' ),
		description: __( 'A field for collecting a localized date and time.', 'omniform' ),
		keywords: [ 'input', 'calendar', 'time' ],
		example: inputTextExample( 'datetime-local', __( 'Date', 'omniform' ) ),
		innerBlocks: inputTextExample( 'datetime-local' ).innerBlocks,
	},
	{
		name: 'field-month',
		category: 'omniform-standard-fields',
		icon: { src: fieldDate },
		title: __( 'Month', 'omniform' ),
		description: __( 'A field for collecting a month formatted date.', 'omniform' ),
		keywords: [ 'input', 'date', 'calendar', 'time' ],
		example: inputTextExample( 'month', __( 'Month', 'omniform' ) ),
		innerBlocks: inputTextExample( 'month' ).innerBlocks,
	},
	{
		name: 'field-tel',
		category: 'omniform-standard-fields',
		icon: { src: fieldTel },
		title: __( 'Phone', 'omniform' ),
		description: __( 'A field for collecting a telephone number.', 'omniform' ),
		keywords: [ 'input', 'telephone', 'number' ],
		example: inputTextExample( 'tel', __( 'Phone Number', 'omniform' ) ),
		innerBlocks: inputTextExample( 'tel' ).innerBlocks,
	},
	{
		name: 'field-time',
		category: 'omniform-standard-fields',
		icon: { src: fieldTime },
		title: __( 'Time', 'omniform' ),
		description: __( 'A field for collecting a formatted time.', 'omniform' ),
		keywords: [ 'input', 'clock' ],
		example: inputTextExample( 'time', __( 'Time', 'omniform' ) ),
		innerBlocks: inputTextExample( 'time' ).innerBlocks,
	},
	{
		name: 'field-week',
		category: 'omniform-standard-fields',
		icon: { src: fieldDate },
		title: __( 'Week', 'omniform' ),
		description: __( 'A field for collecting a week formatted date.', 'omniform' ),
		keywords: [ 'input', 'date', 'calendar', 'time' ],
		example: inputTextExample( 'week', __( 'Week', 'omniform' ) ),
		innerBlocks: inputTextExample( 'week' ).innerBlocks,
	},
	{
		name: 'field-textarea',
		category: 'omniform-standard-fields',
		icon: { src: fieldTextarea },
		title: __( 'Textarea', 'omniform' ),
		description: __( 'A field for long-form text responses.', 'omniform' ),
		keywords: [ 'input', 'long', 'paragraph' ],
		example: {
			attributes: {
				fieldLabel: __( 'Long Text', 'omniform' ),
			},
			innerBlocks: [
				{ name: 'omniform/label' },
				{ name: 'omniform/textarea' },
			],
		},
		innerBlocks: [
			{ name: 'omniform/label' },
			{ name: 'omniform/textarea' },
		],
	},
	{
		name: 'field-select',
		category: 'omniform-standard-fields',
		icon: { src: fieldSelect },
		title: __( 'Select', 'omniform' ),
		description: __( 'A field offering multiple options for a single selection.', 'omniform' ),
		keywords: [ 'input', 'dropdown', 'single', 'choice' ],
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
		category: 'omniform-standard-fields',
		icon: { src: fieldSelect },
		title: __( 'Select Multiple', 'omniform' ),
		description: __( 'A field offering multiple options for multiple selections.', 'omniform' ),
		keywords: [ 'input', 'dropdown', 'multiple', 'choice' ],
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
