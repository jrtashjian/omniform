/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { decodeEntities } from '@wordpress/html-entities';

/**
 * Internal dependencies
 */
import json from './block.json';
import Edit from './edit';
import Save from './save';
// import variations from './variations';
import { fieldGroup } from '../shared/icons';

import './style.scss';

const { name } = json;

registerBlockType( name, {
	edit: Edit,
	icon: { foreground: '#D92E83', src: fieldGroup },
	save: Save,
	// variations,
	example: {
		attributes: {
			fieldLabel: __( 'Field Group', 'omniform' ),
		},
		innerBlocks: [
			{
				attributes: {
					fieldLabel: __( 'Option One', 'omniform' ),
					layout: { type: 'flex', orientation: 'horizontal', justifyContent: 'left' },
				},
				name: 'omniform/field',
				innerBlocks: [
					{ name: 'omniform/input', attributes: { fieldType: 'radio' } },
					{ name: 'omniform/label' },
				],
			},
			{
				attributes: {
					fieldLabel: __( 'Option Two', 'omniform' ),
					layout: { type: 'flex', orientation: 'horizontal', justifyContent: 'left' },
				},
				name: 'omniform/field',
				innerBlocks: [
					{ name: 'omniform/input', attributes: { fieldType: 'radio' } },
					{ name: 'omniform/label' },
				],
			},
			{
				attributes: {
					fieldLabel: __( 'Option Three', 'omniform' ),
					layout: { type: 'flex', orientation: 'horizontal', justifyContent: 'left' },
				},
				name: 'omniform/field',
				innerBlocks: [
					{ name: 'omniform/input', attributes: { fieldType: 'radio' } },
					{ name: 'omniform/label' },
				],
			},
		],
	},
	// Get block name from the option value.
	__experimentalLabel: ( { fieldLabel } ) => fieldLabel && decodeEntities( fieldLabel ),
} );
