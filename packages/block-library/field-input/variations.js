/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	fieldCheckbox,
	// fieldColor,
	fieldDate,
	fieldEmail,
	// fieldFile,
	fieldHidden,
	fieldNumber,
	// fieldPassword,
	fieldRadio,
	// fieldRange,
	fieldSearch,
	fieldTel,
	fieldTime,
	fieldUrl,
} from '../shared/icons';

const variations = [
	{
		name: 'field-email',
		icon: { foreground: '#D92E83', src: fieldEmail },
		title: __( 'Email', 'omniform' ),
		description: __( 'A field for collecting an email address.', 'omnigroup' ),
		attributes: { fieldType: 'email' },
	},
	{
		name: 'field-url',
		icon: { foreground: '#D92E83', src: fieldUrl },
		title: __( 'URL', 'omniform' ),
		description: __( 'A field for collecting an web address.', 'omnigroup' ),
		attributes: { fieldType: 'url' },
	},
	{
		name: 'field-number',
		icon: { foreground: '#D92E83', src: fieldNumber },
		title: __( 'Number', 'omniform' ),
		description: __( 'A field for collecting a number value.', 'omnigroup' ),
		attributes: { fieldType: 'number' },
	},
	{
		name: 'field-checkbox',
		icon: { foreground: '#D92E83', src: fieldCheckbox },
		title: __( 'Checkbox', 'omniform' ),
		description: __( 'A field with can be grouped to give multiple options where multiple choices can be made.', 'omnigroup' ),
		attributes: { fieldType: 'checkbox' },
	},
	{
		name: 'field-radio',
		icon: { foreground: '#D92E83', src: fieldRadio },
		title: __( 'Radio', 'omniform' ),
		description: __( 'A field with can be grouped to give multiple options where a single choice can be made.', 'omnigroup' ),
		attributes: { fieldType: 'radio' },
	},
	// {
	// 	name: 'field-color',
	// 	icon: { foreground: '#D92E83', src: fieldColor },
	// 	title: __( 'Color Picker', 'omniform' ),
	// 	description: __( 'A field for collecting a color value from a color picker.', 'omnigroup' ),
	// 	attributes: { fieldType: 'color' },
	// },
	{
		name: 'field-date',
		icon: { foreground: '#D92E83', src: fieldDate },
		title: __( 'Date', 'omniform' ),
		description: __( 'A field for collecting a formatted date.', 'omnigroup' ),
		attributes: { fieldType: 'date' },
	},
	{
		name: 'field-datetime-local',
		icon: { foreground: '#D92E83', src: fieldDate },
		title: __( 'Datetime', 'omniform' ),
		description: __( 'A field for collecting a localize date and time.', 'omnigroup' ),
		attributes: { fieldType: 'datetime-local' },
	},
	// {
	// 	name: 'field-file',
	// 	icon: { foreground: '#D92E83', src: fieldFile },
	// 	title: __( 'File Upload', 'omniform' ),
	// 	description: __( 'A field for uploading files.', 'omnigroup' ),
	// 	attributes: { fieldType: 'file' },
	// },
	{
		name: 'field-month',
		icon: { foreground: '#D92E83', src: fieldDate },
		title: __( 'Month', 'omniform' ),
		description: __( 'A field for collecting a month formatted date.', 'omnigroup' ),
		attributes: { fieldType: 'month' },
	},
	// {
	// 	name: 'field-password',
	// 	icon: { foreground: '#D92E83', src: fieldPassword },
	// 	title: __( 'Password', 'omniform' ),
	// 	description: __( 'A field for collecting a password.', 'omnigroup' ),
	// 	attributes: { fieldType: 'password' },
	// },
	// {
	// 	name: 'field-range',
	// 	icon: { foreground: '#D92E83', src: fieldRange },
	// 	title: __( 'Range', 'omniform' ),
	// 	description: __( 'A field for selecting a number from a range of numbers.', 'omnigroup' ),
	// 	attributes: { fieldType: 'range' },
	// },
	{
		name: 'field-search',
		icon: { foreground: '#D92E83', src: fieldSearch },
		title: __( 'Search', 'omniform' ),
		description: __( 'A field for collecting a search query.', 'omnigroup' ),
		attributes: { fieldType: 'search' },
	},
	{
		name: 'field-tel',
		icon: { foreground: '#D92E83', src: fieldTel },
		title: __( 'Telephone', 'omniform' ),
		description: __( 'A field for collecting a telephone number.', 'omnigroup' ),
		attributes: { fieldType: 'tel' },
	},
	{
		name: 'field-time',
		icon: { foreground: '#D92E83', src: fieldTime },
		title: __( 'Time', 'omniform' ),
		description: __( 'A field for collecting a formatted time.', 'omnigroup' ),
		attributes: { fieldType: 'time' },
	},
	{
		name: 'field-week',
		icon: { foreground: '#D92E83', src: fieldDate },
		title: __( 'Week', 'omniform' ),
		description: __( 'A field for collecting a week formatted date.', 'omnigroup' ),
		attributes: { fieldType: 'week' },
	},
	{
		name: 'field-hidden',
		icon: { foreground: '#D92E83', src: fieldHidden },
		title: __( 'Hidden', 'omniform' ),
		description: __( '', 'omniform' ),
		attributes: { fieldType: 'hidden' },
	},
	{
		name: 'field-current-user-id',
		icon: { foreground: '#D92E83', src: fieldHidden },
		title: __( 'Current User ID', 'omniform' ),
		description: __( '', 'omniform' ),
		attributes: { fieldType: 'hidden', fieldValue: '{{get_current_user_id}}' },
	},
];

variations.forEach( ( variation ) => {
	variation.isActive = ( blockAttributes, variationAttributes ) =>
		blockAttributes.fieldType ===
		variationAttributes.fieldType;
} );

export default variations;
