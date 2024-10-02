/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import json from './block.json';
import Edit from './edit';
import Save from './save';
import variations from './variations';
import { conditionalGroup as iconConditionalGroup } from '../shared/icons';

import './index.scss';

const { name } = json;

registerBlockType( name, {
	edit: Edit,
	save: Save,
	icon: { foreground: '#D92E83', src: iconConditionalGroup },
	variations,
} );
