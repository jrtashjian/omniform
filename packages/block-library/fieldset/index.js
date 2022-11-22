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
			label: __( 'Field Group', 'inquirywp' ),
		},
		innerBlocks: [
			{
				attributes: {
					type: 'checkbox',
					label: __( 'Option One', 'inquirywp' ),
				},
				name: 'inquirywp/field-input',
			},
			{
				attributes: {
					type: 'checkbox',
					label: __( 'Option Two', 'inquirywp' ),
				},
				name: 'inquirywp/field-input',
			},
			{
				attributes: {
					type: 'checkbox',
					label: __( 'Option Three', 'inquirywp' ),
				},
				name: 'inquirywp/field-input',
			},
		],
	},
} );
