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

const variations = [
	{
		// This is a default to improve variation transforms.
		name: 'field-text',
		icon: { src: fieldInput },
		title: __( 'Short Text', 'omniform' ),
		description: __( 'A text field for brief responses.', 'omnigroup' ),
		attributes: { fieldType: 'text', fieldValue: undefined },
		scope: [ 'transform' ],
	},
	{
		name: 'field-email',
		icon: { src: fieldEmail },
		title: __( 'Email', 'omniform' ),
		description: __( 'A field for collecting an email address.', 'omnigroup' ),
		attributes: { fieldType: 'email', fieldValue: undefined },
	},
	{
		name: 'field-url',
		icon: { src: fieldUrl },
		title: __( 'URL', 'omniform' ),
		description: __( 'A field for collecting a website address or URL.', 'omnigroup' ),
		attributes: { fieldType: 'url', fieldValue: undefined },
	},
	{
		name: 'field-number',
		icon: { src: fieldNumber },
		title: __( 'Number', 'omniform' ),
		description: __( 'A field for collecting a numerical value.', 'omnigroup' ),
		attributes: { fieldType: 'number', fieldValue: undefined },
	},
	{
		name: 'field-checkbox',
		icon: { src: fieldCheckbox },
		title: __( 'Checkbox', 'omniform' ),
		description: __( 'A field that allows for multiple options and choices.', 'omnigroup' ),
		attributes: { fieldType: 'checkbox', isRequired: false, fieldValue: undefined },
	},
	{
		name: 'field-radio',
		icon: { src: fieldRadio },
		title: __( 'Radio', 'omniform' ),
		description: __( 'A field that can be grouped, from which a single choice can be made.', 'omnigroup' ),
		attributes: { fieldType: 'radio', isRequired: false, fieldValue: undefined },
	},
	{
		name: 'field-date',
		icon: { src: fieldDate },
		title: __( 'Date', 'omniform' ),
		description: __( 'A field for collecting a formatted date.', 'omnigroup' ),
		attributes: { fieldType: 'date', fieldValue: undefined },
	},
	{
		name: 'field-datetime-local',
		icon: { src: fieldDate },
		title: __( 'Datetime', 'omniform' ),
		description: __( 'A field for collecting a localize date and time.', 'omnigroup' ),
		attributes: { fieldType: 'datetime-local', fieldValue: undefined },
	},
	{
		name: 'field-month',
		icon: { src: fieldDate },
		title: __( 'Month', 'omniform' ),
		description: __( 'A field for collecting a month formatted date.', 'omnigroup' ),
		attributes: { fieldType: 'month', fieldValue: undefined },
	},
	{
		name: 'field-tel',
		icon: { src: fieldTel },
		title: __( 'Phone', 'omniform' ),
		description: __( 'A field for collecting a telephone number.', 'omnigroup' ),
		attributes: { fieldType: 'tel', fieldValue: undefined },
	},
	{
		name: 'field-time',
		icon: { src: fieldTime },
		title: __( 'Time', 'omniform' ),
		description: __( 'A field for collecting a formatted time.', 'omnigroup' ),
		attributes: { fieldType: 'time', fieldValue: undefined },
	},
	{
		name: 'field-week',
		icon: { src: fieldDate },
		title: __( 'Week', 'omniform' ),
		description: __( 'A field for collecting a week formatted date.', 'omnigroup' ),
		attributes: { fieldType: 'week', fieldValue: undefined },
	},
];

variations.forEach( ( variation ) => {
	variation.isActive = ( blockAttributes, variationAttributes ) =>
		blockAttributes.fieldType ===
		variationAttributes.fieldType;

	if ( ! variation.scope ) {
		variation.scope = [ 'inserter', 'block', 'transform' ];
	}
} );

export default variations;
