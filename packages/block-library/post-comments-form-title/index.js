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
import { label as iconLabel } from '../shared/icons';

const { name } = json;

registerBlockType( name, {
	edit: Edit,
	icon: { foreground: '#D92E83', src: iconLabel },
} );