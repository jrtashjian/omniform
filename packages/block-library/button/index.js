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
import variations from './variations';
import { Button } from '../shared/icons';

import './style.scss';
import './index.scss';

const { name } = json;

registerBlockType( name, {
	edit: Edit,
	icon: { foreground: '#D92E83', src: Button },
	example: {},
	variations,
	merge( attributes, attributesToMerge ) {
		return {
			buttonLabel:
				( attributes.buttonLabel || '' ) +
				( attributesToMerge.buttonLabel || '' ),
		};
	},
	// Get block name from the option value.
	__experimentalLabel: ( { buttonLabel } ) => buttonLabel && decodeEntities( buttonLabel ),
} );
