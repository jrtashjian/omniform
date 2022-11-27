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
import { fieldTextarea as icon } from '../shared/icons';

import './style.scss';
import './index.scss';

const { name } = json;

registerBlockType( name, {
	edit: Edit,
	icon,
	example: {
		attributes: {
			label: __( 'Example input', 'omniform' ),
		},
	},
	merge: ( attributes, attributesToMerge ) => {
		return {
			label:
				( attributes.label || '' ) +
				( attributesToMerge.label || '' ),
		};
	},
	// Get block name from the option value.
	__experimentalLabel: ( { label } ) => label && decodeEntities( label ),
} );
