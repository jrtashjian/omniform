/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	fieldCheckbox,
	fieldColor,
	fieldDate,
	fieldEmail,
	fieldFile,
	fieldHidden,
	fieldHoneypot,
	fieldNumber,
	fieldPassword,
	fieldRadio,
	fieldRange,
	fieldSearch,
	fieldTel,
	fieldTime,
	fieldUrl,
} from '../shared/icons';

const variations = [
	{
		name: 'field-email',
		icon: fieldEmail,
		title: __( 'Email', 'omniform' ),
		description: __( 'A field for collecting an email address.', 'omnigroup' ),
		attributes: { type: 'email' },
	},
	{
		name: 'field-url',
		icon: fieldUrl,
		title: __( 'URL', 'omniform' ),
		description: __( 'A field for collecting an web address.', 'omnigroup' ),
		attributes: { type: 'url' },
	},
	{
		name: 'field-number',
		icon: fieldNumber,
		title: __( 'Number', 'omniform' ),
		description: __( 'A field for collecting a number value.', 'omnigroup' ),
		attributes: { type: 'number' },
	},
	{
		name: 'field-checkbox',
		icon: fieldCheckbox,
		title: __( 'Checkbox', 'omniform' ),
		description: __( 'A field with can be grouped to give multiple options where multiple choices can be made.', 'omnigroup' ),
		attributes: { type: 'checkbox' },
	},
	{
		name: 'field-radio',
		icon: fieldRadio,
		title: __( 'Radio', 'omniform' ),
		description: __( 'A field with can be grouped to give multiple options where a single choice can be made.', 'omnigroup' ),
		attributes: { type: 'radio' },
	},
	{
		name: 'field-color',
		icon: fieldColor,
		title: __( 'Color Picker', 'omniform' ),
		description: __( 'A field for collecting a color value from a color picker.', 'omnigroup' ),
		attributes: { type: 'color' },
	},
	{
		name: 'field-date',
		icon: fieldDate,
		title: __( 'Date', 'omniform' ),
		description: __( 'A field for collecting a formatted date.', 'omnigroup' ),
		attributes: { type: 'date' },
	},
	{
		name: 'field-datetime-local',
		icon: fieldDate,
		title: __( 'Datetime', 'omniform' ),
		description: __( 'A field for collecting a localize date and time.', 'omnigroup' ),
		attributes: { type: 'datetime-local' },
	},
	{
		name: 'field-file',
		icon: fieldFile,
		title: __( 'File Upload', 'omniform' ),
		description: __( 'A field for uploading files.', 'omnigroup' ),
		attributes: { type: 'file' },
	},
	{
		name: 'field-month',
		icon: fieldDate,
		title: __( 'Month', 'omniform' ),
		description: __( 'A field for collecting a month formatted date.', 'omnigroup' ),
		attributes: { type: 'month' },
	},
	{
		name: 'field-password',
		icon: fieldPassword,
		title: __( 'Password', 'omniform' ),
		description: __( 'A field for collecting a password.', 'omnigroup' ),
		attributes: { type: 'password' },
	},
	{
		name: 'field-range',
		icon: fieldRange,
		title: __( 'Range', 'omniform' ),
		description: __( 'A field for selecting a number from a range of numbers.', 'omnigroup' ),
		attributes: { type: 'range' },
	},
	{
		name: 'field-search',
		icon: fieldSearch,
		title: __( 'Search', 'omniform' ),
		description: __( 'A field for collecting a search query.', 'omnigroup' ),
		attributes: { type: 'search' },
	},
	{
		name: 'field-tel',
		icon: fieldTel,
		title: __( 'Telephone', 'omniform' ),
		description: __( 'A field for collecting a telephone number.', 'omnigroup' ),
		attributes: { type: 'tel' },
	},
	{
		name: 'field-time',
		icon: fieldTime,
		title: __( 'Time', 'omniform' ),
		description: __( 'A field for collecting a formatted time.', 'omnigroup' ),
		attributes: { type: 'time' },
	},
	{
		name: 'field-week',
		icon: fieldDate,
		title: __( 'Week', 'omniform' ),
		description: __( 'A field for collecting a week formatted date.', 'omnigroup' ),
		attributes: { type: 'week' },
	},
	{
		name: 'field-hidden',
		icon: fieldHidden,
		title: __( 'Hidden', 'omniform' ),
		description: __( '', 'omniform' ),
		attributes: { type: 'hidden' },
	},
	{
		name: 'field-honeypot',
		icon: fieldHoneypot,
		title: __( 'Honeypot', 'omniform' ),
		description: __( '', 'omnigroup' ),
		attributes: { type: 'hidden', isHoneypot: true },
	},
];

variations.forEach( ( variation ) => {
	variation.isActive = ( blockAttributes, variationAttributes ) =>
		blockAttributes.type === variationAttributes.type &&
		blockAttributes.isHoneypot === variationAttributes.isHoneypot;
} );

export default variations;
