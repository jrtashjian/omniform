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
import { fieldHidden } from '../shared/icons';

import './index.scss';

const { name } = json;

registerBlockType( name, {
	edit: Edit,
	icon: { foreground: '#D92E83', src: fieldHidden },
	variations,
	example: {},
	// Return the title of the variation if fieldName is in variations, otherwise return fieldName.
	__experimentalLabel: ( { fieldName, fieldValue } ) => {
		const variation = variations.find( ( { attributes } ) =>
			attributes.fieldName === fieldName &&
			attributes.fieldValue === fieldValue
		);
		return variation ? decodeEntities( variation.title ) : decodeEntities( fieldName );
	},
} );
