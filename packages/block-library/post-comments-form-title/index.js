/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import json from './block.json';
import Edit from './edit';
import { label as iconLabel } from '../shared/icons';

import './style.scss';

const { name } = json;

registerBlockType( name, {
	edit: Edit,
	icon: { foreground: '#D92E83', src: iconLabel },
} );
