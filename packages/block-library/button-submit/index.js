/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import json from './block.json';
import Edit from './edit';
import { Button as icon } from '../shared/icons';

import './style.scss';
import './index.scss';

const { name } = json;

registerBlockType( name, {
	edit: Edit,
	icon,
	example: {},
	merge( attributes, attributesToMerge ) {
		return {
			text:
				( attributes.text || '' ) +
				( attributesToMerge.text || '' ),
		};
	},
} );
