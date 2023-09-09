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
		name: 'input-text',
		icon: { src: fieldInput },
		title: __( 'Text', 'omniform' ),
		description: __( 'A field for brief text entry.', 'omniform' ),
		attributes: { fieldType: 'text' },
		scope: [ 'transform' ],
	},
	{
		name: 'input-email',
		icon: { src: fieldEmail },
		title: __( 'Email', 'omniform' ),
		description: __( 'A field for collecting an email address.', 'omniform' ),
		attributes: { fieldType: 'email' },
	},
	{
		name: 'input-url',
		icon: { src: fieldUrl },
		title: __( 'URL', 'omniform' ),
		description: __( 'A field for collecting a website address or URL.', 'omniform' ),
		attributes: { fieldType: 'url' },
	},
	{
		name: 'input-number',
		icon: { src: fieldNumber },
		title: __( 'Number', 'omniform' ),
		description: __( 'A field for collecting a numerical value.', 'omniform' ),
		attributes: { fieldType: 'number' },
	},
	{
		name: 'input-checkbox',
		icon: { src: fieldCheckbox },
		title: __( 'Checkbox', 'omniform' ),
		description: __( 'A field for selecting multiple options or choices.', 'omniform' ),
		attributes: { fieldType: 'checkbox' },
	},
	{
		name: 'input-radio',
		icon: { src: fieldRadio },
		title: __( 'Radio', 'omniform' ),
		description: __( 'A field in a group allowing for one selected choice.', 'omniform' ),
		attributes: { fieldType: 'radio' },
	},
	{
		name: 'input-date',
		icon: { src: fieldDate },
		title: __( 'Date', 'omniform' ),
		description: __( 'A field for collecting a formatted date.', 'omniform' ),
		attributes: { fieldType: 'date' },
	},
	{
		name: 'input-datetime-local',
		icon: { src: fieldDate },
		title: __( 'Datetime', 'omniform' ),
		description: __( 'A field for collecting a localized date and time.', 'omniform' ),
		attributes: { fieldType: 'datetime-local' },
	},
	{
		name: 'input-month',
		icon: { src: fieldDate },
		title: __( 'Month', 'omniform' ),
		description: __( 'A field for collecting a month formatted date.', 'omniform' ),
		attributes: { fieldType: 'month' },
	},
	{
		name: 'input-tel',
		icon: { src: fieldTel },
		title: __( 'Phone', 'omniform' ),
		description: __( 'A field for collecting a telephone number.', 'omniform' ),
		attributes: { fieldType: 'tel' },
	},
	{
		name: 'input-time',
		icon: { src: fieldTime },
		title: __( 'Time', 'omniform' ),
		description: __( 'A field for collecting a formatted time.', 'omniform' ),
		attributes: { fieldType: 'time' },
	},
	{
		name: 'input-week',
		icon: { src: fieldDate },
		title: __( 'Week', 'omniform' ),
		description: __( 'A field for collecting a week formatted date.', 'omniform' ),
		attributes: { fieldType: 'week' },
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
