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
} from '../shared/icons';

/**
 * An example of a text input field.
 *
 * @param {string} fieldType The type of input field.
 * @return {Object} The example.
 */
const inputTextExample = ( fieldType ) => ( {
	attributes: {
		fieldLabel: __( 'Field Label', 'omniform' ),
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
 * @param {string} fieldType The type of input field.
 * @return {Object} The example.
 */
const inputOptionExample = ( fieldType ) => ( {
	attributes: {
		fieldLabel: __( 'Field Label', 'omniform' ),
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
		title: __( 'Short Text', 'omniform' ),
		description: __( 'A text field for brief responses.', 'omniform' ),
		example: inputTextExample( 'text' ),
		innerBlocks: inputTextExample( 'text' ).innerBlocks,
	},
	{
		name: 'field-email',
		icon: { src: fieldEmail },
		title: __( 'Email', 'omniform' ),
		description: __( 'A field for collecting an email address.', 'omniform' ),
		example: inputTextExample( 'email' ),
		innerBlocks: inputTextExample( 'email' ).innerBlocks,
	},
	{
		name: 'field-url',
		icon: { src: fieldUrl },
		title: __( 'URL', 'omniform' ),
		description: __( 'A field for collecting a website address or URL.', 'omniform' ),
		example: inputTextExample( 'url' ),
		innerBlocks: inputTextExample( 'url' ).innerBlocks,
	},
	{
		name: 'field-number',
		icon: { src: fieldNumber },
		title: __( 'Number', 'omniform' ),
		description: __( 'A field for collecting a numerical value.', 'omniform' ),
		example: inputTextExample( 'number' ),
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
		example: inputOptionExample( 'checkbox' ),
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
		example: inputOptionExample( 'radio' ),
		innerBlocks: inputOptionExample( 'radio' ).innerBlocks,
	},
	{
		name: 'field-date',
		icon: { src: fieldDate },
		title: __( 'Date', 'omniform' ),
		description: __( 'A field for collecting a formatted date.', 'omniform' ),
		example: inputTextExample( 'date' ),
		innerBlocks: inputTextExample( 'date' ).innerBlocks,
	},
	{
		name: 'field-datetime-local',
		icon: { src: fieldDate },
		title: __( 'Datetime', 'omniform' ),
		description: __( 'A field for collecting a localize date and time.', 'omniform' ),
		example: inputTextExample( 'datetime-local' ),
		innerBlocks: inputTextExample( 'datetime-local' ).innerBlocks,
	},
	{
		name: 'field-month',
		icon: { src: fieldDate },
		title: __( 'Month', 'omniform' ),
		description: __( 'A field for collecting a month formatted date.', 'omniform' ),
		example: inputTextExample( 'month' ),
		innerBlocks: inputTextExample( 'month' ).innerBlocks,
	},
	{
		name: 'field-tel',
		icon: { src: fieldTel },
		title: __( 'Phone', 'omniform' ),
		description: __( 'A field for collecting a telephone number.', 'omniform' ),
		example: inputTextExample( 'tel' ),
		innerBlocks: inputTextExample( 'tel' ).innerBlocks,
	},
	{
		name: 'field-time',
		icon: { src: fieldTime },
		title: __( 'Time', 'omniform' ),
		description: __( 'A field for collecting a formatted time.', 'omniform' ),
		example: inputTextExample( 'time' ),
		innerBlocks: inputTextExample( 'time' ).innerBlocks,
	},
	{
		name: 'field-week',
		icon: { src: fieldDate },
		title: __( 'Week', 'omniform' ),
		description: __( 'A field for collecting a week formatted date.', 'omniform' ),
		example: inputTextExample( 'week' ),
		innerBlocks: inputTextExample( 'week' ).innerBlocks,
	},
];

export default variations;
