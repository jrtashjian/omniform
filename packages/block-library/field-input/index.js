/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import json from './block.json';
import Edit from './edit';

import './style.scss';
import './index.scss';

const { name } = json;

registerBlockType( name, {
	edit: Edit,
	variations: [
		{
			name: 'field-text',
			title: 'field-text',
			attributes: {
				type: 'text',
			},
		},
		{
			name: 'field-email',
			title: 'field-email',
			attributes: {
				type: 'email',
			},
		},
		{
			name: 'field-url',
			title: 'field-url',
			attributes: {
				type: 'url',
			},
		},
		{
			name: 'field-password',
			title: 'field-password',
			attributes: {
				type: 'password',
			},
		},
	],
} );
