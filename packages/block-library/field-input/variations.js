/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

const variations = [
	{
		name: 'field-email',
		title: __( 'field-email', 'omniform' ),
		attributes: { type: 'email' },
	},
	{
		name: 'field-url',
		title: __( 'field-url', 'omniform' ),
		attributes: { type: 'url' },
	},
	{
		name: 'field-number',
		title: __( 'field-number', 'omniform' ),
		attributes: { type: 'number' },
	},
	{
		name: 'field-checkbox',
		title: __( 'field-checkbox', 'omniform' ),
		attributes: { type: 'checkbox' },
	},
	{
		name: 'field-radio',
		title: __( 'field-radio', 'omniform' ),
		attributes: { type: 'radio' },
	},
	{
		name: 'field-color',
		title: __( 'field-color', 'omniform' ),
		attributes: { type: 'color' },
	},
	{
		name: 'field-date',
		title: __( 'field-date', 'omniform' ),
		attributes: { type: 'date' },
	},
	{
		name: 'field-datetime-local',
		title: __( 'field-datetime-local', 'omniform' ),
		attributes: { type: 'datetime-local' },
	},
	{
		name: 'field-file',
		title: __( 'field-file', 'omniform' ),
		attributes: { type: 'file' },
	},
	{
		name: 'field-month',
		title: __( 'field-month', 'omniform' ),
		attributes: { type: 'month' },
	},
	{
		name: 'field-password',
		title: __( 'field-password', 'omniform' ),
		attributes: { type: 'password' },
	},
	{
		name: 'field-range',
		title: __( 'field-range', 'omniform' ),
		attributes: { type: 'range' },
	},
	{
		name: 'field-search',
		title: __( 'field-search', 'omniform' ),
		attributes: { type: 'search' },
	},
	{
		name: 'field-tel',
		title: __( 'field-tel', 'omniform' ),
		attributes: { type: 'tel' },
	},
	{
		name: 'field-time',
		title: __( 'field-time', 'omniform' ),
		attributes: { type: 'time' },
	},
	{
		name: 'field-week',
		title: __( 'field-week', 'omniform' ),
		attributes: { type: 'week' },
	},
];

variations.forEach( ( variation ) => {
	variation.isActive = ( blockAttributes, variationAttributes ) =>
		blockAttributes.type ===
	variationAttributes.type;
} );

export default variations;
