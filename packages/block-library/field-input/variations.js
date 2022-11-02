/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

const variations = [
	{
		name: 'field-text',
		title: 'field-text',
		attributes: { type: 'text' },
	},
	{
		name: 'field-email',
		title: 'field-email',
		attributes: { type: 'email' },
	},
	{
		name: 'field-url',
		title: 'field-url',
		attributes: { type: 'url' },
	},
	{
		name: 'field-password',
		title: 'field-password',
		attributes: { type: 'password' },
	},
	{
		name: 'field-number',
		title: 'field-number',
		attributes: { type: 'number' },
	},
	{
		name: 'field-date',
		title: 'field-date',
		attributes: { type: 'date' },
	},
	{
		name: 'field-datetime-local',
		title: 'field-datetime-local',
		attributes: { type: 'datetime-local' },
	},
	{
		name: 'field-month',
		title: 'field-month',
		attributes: { type: 'month' },
	},
	{
		name: 'field-search',
		title: 'field-search',
		attributes: { type: 'search' },
	},
	{
		name: 'field-tel',
		title: 'field-tel',
		attributes: { type: 'tel' },
	},
	{
		name: 'field-time',
		title: 'field-time',
		attributes: { type: 'time' },
	},
	{
		name: 'field-week',
		title: 'field-week',
		attributes: { type: 'week' },
	},
];

variations.forEach( ( variation ) => {
	variation.isActive = ( blockAttributes, variationAttributes ) =>
		blockAttributes.type ===
		variationAttributes.type;
} );

export default variations;
