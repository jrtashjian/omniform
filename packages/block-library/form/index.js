/**
 * External dependencies
 */
import { capitalCase } from 'change-case';

/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import { select } from '@wordpress/data';
import { decodeEntities } from '@wordpress/html-entities';

/**
 * Internal dependencies
 */
import json from './block.json';
import edit from './edit';

import './style.scss';
import './index.scss';

const { name } = json;

registerBlockType( name, {
	edit,
	// Get block name from the post name.
	__experimentalLabel: ( { ref } ) => {
		if ( ! ref ) {
			return;
		}

		const entity = select( 'core' ).getEntityRecord(
			'postType',
			'inquirywp_form',
			ref
		);
		if ( ! entity ) {
			return;
		}

		return (
			decodeEntities( entity.title?.rendered ) ||
			capitalCase( entity.slug )
		);
	},
} );
