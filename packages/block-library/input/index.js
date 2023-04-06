/**
 * External dependencies
 */
import { startCase } from 'lodash';

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
import { fieldInput } from '../shared/icons';

import './style.scss';

const { name } = json;

registerBlockType( name, {
	edit: Edit,
	icon: { foreground: '#D92E83', src: fieldInput },
	variations,
	example: {},
	// Get block name from the option value.
	__experimentalLabel: ( { fieldType } ) => startCase( name.replace( 'omniform', fieldType ) ),
} );
