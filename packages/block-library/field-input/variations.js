/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

const variations = [
	{
		name: 'field-email',
		title: __( 'field-email', 'inquirywp' ),
		attributes: { type: 'email' },
	},
	{
		name: 'field-url',
		title: __( 'field-url', 'inquirywp' ),
		attributes: { type: 'url' },
	},
	{
		name: 'field-number',
		title: __( 'field-number', 'inquirywp' ),
		attributes: { type: 'number' },
	},
	{
		name: 'field-checkbox',
		title: __( 'field-checkbox', 'inquirywp' ),
		attributes: { type: 'checkbox' },
	},
	{
		name: 'field-radio',
		title: __( 'field-radio', 'inquirywp' ),
		attributes: { type: 'radio' },
	},
	{
		name: 'field-color',
		title: __( 'field-color', 'inquirywp' ),
		attributes: { type: 'color' },
	},
	{
		name: 'field-date',
		title: __( 'field-date', 'inquirywp' ),
		attributes: { type: 'date' },
	},
	{
		name: 'field-datetime-local',
		title: __( 'field-datetime-local', 'inquirywp' ),
		attributes: { type: 'datetime-local' },
	},
	{
		name: 'field-file',
		title: __( 'field-file', 'inquirywp' ),
		attributes: { type: 'file' },
	},
	{
		name: 'field-month',
		title: __( 'field-month', 'inquirywp' ),
		attributes: { type: 'month' },
	},
	{
		name: 'field-password',
		title: __( 'field-password', 'inquirywp' ),
		attributes: { type: 'password' },
	},
	{
		name: 'field-range',
		title: __( 'field-range', 'inquirywp' ),
		attributes: { type: 'range' },
	},
	{
		name: 'field-search',
		title: __( 'field-search', 'inquirywp' ),
		attributes: { type: 'search' },
	},
	{
		name: 'field-tel',
		title: __( 'field-tel', 'inquirywp' ),
		attributes: { type: 'tel' },
	},
	{
		name: 'field-time',
		title: __( 'field-time', 'inquirywp' ),
		attributes: { type: 'time' },
	},
	{
		name: 'field-week',
		title: __( 'field-week', 'inquirywp' ),
		attributes: { type: 'week' },
	},
];

variations.forEach( ( variation ) => {
	variation.isActive = ( blockAttributes, variationAttributes ) =>
		blockAttributes.type ===
	variationAttributes.type;
} );

export default variations;
