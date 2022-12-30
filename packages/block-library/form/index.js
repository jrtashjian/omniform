/**
 * External dependencies
 */
import { capitalCase } from 'change-case';

/**
 * WordPress dependencies
 */
import { registerBlockType, registerBlockStyle } from '@wordpress/blocks';
import { select } from '@wordpress/data';
import { decodeEntities } from '@wordpress/html-entities';
import { addFilter } from '@wordpress/hooks';

/**
 * Internal dependencies
 */
import json from './block.json';
import edit from './edit';
import { POST_TYPE } from '../shared/constants';
import { form } from '../shared/icons';

import './style.scss';
import './index.scss';

const { name } = json;

registerBlockType( name, {
	edit,
	icon: { foreground: '#D92E83', src: form },
	// Get block name from the post name.
	__experimentalLabel: ( { ref } ) => {
		if ( ! ref ) {
			return;
		}

		const entity = select( 'core' ).getEntityRecord(
			'postType',
			POST_TYPE,
			ref
		);
		if ( ! entity ) {
			return;
		}

		return (
			decodeEntities( entity.title?.rendered ) ||
			decodeEntities( entity.title?.raw ) ||
			capitalCase( entity.slug )
		);
	},
} );

// Prevent form block from being inserted into an omniform post type unless editing the template.
addFilter(
	'blockEditor.__unstableCanInsertBlockType',
	'removeOmniformFromOmniformPostType',
	(
		canInsert,
		blockType,
	) => {
		if (
			'omniform/form' !== blockType.name ||
			'omniform' !== select( 'core/editor' ).getCurrentPostType()
		) {
			return canInsert;
		}

		return select( 'core/edit-post' ).isEditingTemplate();
	}
);

registerBlockStyle( 'omniform/form', {
	name: 'inverted',
	label: 'Inverted',
} );
