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

import './style.scss';

const { name } = json;

registerBlockType( name, {
	edit: Edit,
	example: {
		attributes: {
			fieldLabel: __( 'Field Label', 'omniform' ),
		},
	},
} );
