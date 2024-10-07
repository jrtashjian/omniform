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
import variations from './variations';
import { iconSuccess } from '../shared/icons';

import './style.scss';
import './index.scss';

const { name } = json;

registerBlockType( name, {
	edit: Edit,
	icon: iconSuccess,
	example: {
		attributes: {
			messageContent: __( 'Sharing some info you might find helpful.', 'omniform' ),
		},
	},
	variations,
} );
