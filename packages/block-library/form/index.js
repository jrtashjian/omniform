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
import { addFilter } from '@wordpress/hooks';
import { InnerBlocks } from '@wordpress/block-editor';

/**
 * Internal dependencies
 */
import json from './block.json';
import StandardForm from './edit/standard-form';
import StandaloneForm from './edit/standalone-form';
import variations from './variations';
import { POST_TYPE } from '../shared/constants';
import { form } from '../shared/icons';

import './document';

import './index.scss';
import './style.scss';

const { name } = json;

registerBlockType( name, {
	edit: ( props ) => {
		const Edit = props.attributes.ref ? StandardForm : StandaloneForm;
		return <Edit { ...props } />;
	},
	save: ( { ref } ) => ref ? null : <InnerBlocks.Content />,
	icon: { foreground: '#D92E83', src: form },
	variations,
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
	'omniform/with-block-insertion-restrictions',
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
