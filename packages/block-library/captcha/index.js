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

const { name } = json;

registerBlockType( name, {
	edit: Edit,
	variations,
} );
