/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import { decodeEntities } from '@wordpress/html-entities';

/**
 * Internal dependencies
 */
import json from './block.json';
import Edit from './edit';
import Save from './save';
import transforms from './transforms';
import variations from './variations';
import { field as iconField } from '../shared/icons';

import './style.scss';

const { name } = json;

registerBlockType( name, {
	edit: Edit,
	save: Save,
	icon: { foreground: '#D92E83', src: iconField },
	example: {
		attributes: {
			fieldLabel: 'Field Label',
		},
		innerBlocks: [
			{ name: 'omniform/label' },
			{ name: 'omniform/input' },
		],
	},
	transforms,
	variations,
	// Get block name from the option value.
	__experimentalLabel: ( { fieldLabel } ) => fieldLabel && decodeEntities( fieldLabel ),
} );
