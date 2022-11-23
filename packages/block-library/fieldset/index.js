/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import json from './block.json';
import Edit from './edit';
import Save from './save';

import './style.scss';
import './index.scss';

const { name } = json;

registerBlockType( name, {
	edit: Edit,
	save: Save,
	example: {
		attributes: {
			label: __( 'Field Group', 'omniform' ),
		},
		innerBlocks: [
			{
				attributes: {
					type: 'checkbox',
					label: __( 'Option One', 'omniform' ),
				},
				name: 'omniform/field-input',
			},
			{
				attributes: {
					type: 'checkbox',
					label: __( 'Option Two', 'omniform' ),
				},
				name: 'omniform/field-input',
			},
			{
				attributes: {
					type: 'checkbox',
					label: __( 'Option Three', 'omniform' ),
				},
				name: 'omniform/field-input',
			},
		],
	},
} );
