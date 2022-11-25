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
import { FORM_POST_TYPE } from '../shared/constants';
import { form as icon } from '../shared/icons';

import './style.scss';
import './index.scss';

const { name } = json;

registerBlockType( name, {
	edit,
	icon,
	// Get block name from the post name.
	__experimentalLabel: ( { ref } ) => {
		if ( ! ref ) {
			return;
		}

		const entity = select( 'core' ).getEntityRecord(
			'postType',
			FORM_POST_TYPE,
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
