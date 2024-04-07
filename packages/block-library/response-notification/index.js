/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import json from './block.json';
import Edit from './edit';
import variations from './variations';
import { iconSuccess } from '../shared/icons';

import './index.scss';

const { name } = json;

registerBlockType( name, {
	edit: Edit,
	icon: iconSuccess,
	example: {},
	variations,
} );
