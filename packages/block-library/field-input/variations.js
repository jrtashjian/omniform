/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

const variations = [
	{
		name: 'field-email',
		title: __( 'Email', 'omniform' ),
		description: __( 'A field for collecting an email address.', 'omnigroup' ),
		attributes: { type: 'email' },
	},
	{
		name: 'field-url',
		title: __( 'URL', 'omniform' ),
		description: __( 'A field for collecting an web address.', 'omnigroup' ),
		attributes: { type: 'url' },
	},
	{
		name: 'field-number',
		title: __( 'Number', 'omniform' ),
		description: __( 'A field for collecting a number value.', 'omnigroup' ),
		attributes: { type: 'number' },
	},
	{
		name: 'field-checkbox',
		title: __( 'Checkbox', 'omniform' ),
		description: __( 'A field with can be grouped to give multiple options where multiple choices can be made.', 'omnigroup' ),
		attributes: { type: 'checkbox' },
	},
	{
		name: 'field-radio',
		title: __( 'Radio', 'omniform' ),
		description: __( 'A field with can be grouped to give multiple options where a single choice can be made.', 'omnigroup' ),
		attributes: { type: 'radio' },
	},
	{
		name: 'field-color',
		title: __( 'Color Picker', 'omniform' ),
		description: __( 'A field for collecting a color value from a color picker.', 'omnigroup' ),
		attributes: { type: 'color' },
	},
	{
		name: 'field-date',
		title: __( 'Date', 'omniform' ),
		description: __( 'A field for collecting a formatted date.', 'omnigroup' ),
		attributes: { type: 'date' },
	},
	{
		name: 'field-datetime-local',
		title: __( 'Datetime', 'omniform' ),
		description: __( 'A field for collecting a localize date and time.', 'omnigroup' ),
		attributes: { type: 'datetime-local' },
	},
	{
		name: 'field-file',
		title: __( 'File Upload', 'omniform' ),
		description: __( 'A field for uploading files.', 'omnigroup' ),
		attributes: { type: 'file' },
	},
	{
		name: 'field-month',
		title: __( 'Month', 'omniform' ),
		description: __( 'A field for collecting a month formatted date.', 'omnigroup' ),
		attributes: { type: 'month' },
	},
	{
		name: 'field-password',
		title: __( 'Password', 'omniform' ),
		description: __( 'A field for collecting a password.', 'omnigroup' ),
		attributes: { type: 'password' },
	},
	{
		name: 'field-range',
		title: __( 'Range', 'omniform' ),
		description: __( 'A field for selecting a number from a range of numbers.', 'omnigroup' ),
		attributes: { type: 'range' },
	},
	{
		name: 'field-search',
		title: __( 'Search', 'omniform' ),
		description: __( 'A field for collecting a search query.', 'omnigroup' ),
		attributes: { type: 'search' },
	},
	{
		name: 'field-tel',
		title: __( 'Telephone', 'omniform' ),
		description: __( 'A field for collecting a telephone number.', 'omnigroup' ),
		attributes: { type: 'tel' },
	},
	{
		name: 'field-time',
		title: __( 'Time', 'omniform' ),
		description: __( 'A field for collecting a formatted time.', 'omnigroup' ),
		attributes: { type: 'time' },
	},
	{
		name: 'field-week',
		title: __( 'Week', 'omniform' ),
		description: __( 'A field for collecting a week formatted date.', 'omnigroup' ),
		attributes: { type: 'week' },
	},
];

variations.forEach( ( variation ) => {
	variation.isActive = ( blockAttributes, variationAttributes ) =>
		blockAttributes.type ===
	variationAttributes.type;
} );

export default variations;
