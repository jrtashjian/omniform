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
import { createHigherOrderComponent } from '@wordpress/compose';

/**
 * Internal dependencies
 */
import json from './block.json';
import edit from './edit';
import { POST_TYPE } from '../shared/constants';
import { form } from '../shared/icons';

import './index.scss';
import './style.scss';

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

// Restricts the blocks that can be added as child blocks within the form block.
addFilter(
	'editor.BlockEdit',
	'omniform/with-child-block-restrictions',
	createHigherOrderComponent( ( BlockEdit ) => {
		return ( props ) => {
			if (
				[ 'core/group', 'core/cover', 'core/column' ].includes( props.name ) &&
				!! select( 'core/block-editor' ).getBlockParentsByBlockName( props.clientId, 'omniform/form' ).length
			) {
				props.attributes.allowedBlocks = [
					'omniform/button',
					'omniform/fieldset',
					'omniform/field',
					'omniform/label',
					'omniform/input',
					'omniform/textarea',
					'omniform/select',
					'omniform/select-group',
					'omniform/select-option',
					'omniform/captcha',
					'core/audio',
					'core/block',
					'core/code',
					'core/column',
					'core/columns',
					'core/cover',
					'core/file',
					'core/gallery',
					'core/group',
					'core/heading',
					'core/image',
					'core/list-item',
					'core/list',
					'core/missing',
					'core/paragraph',
					'core/pattern',
					'core/preformatted',
					'core/separator',
					'core/site-logo',
					'core/site-tagline',
					'core/site-title',
					'core/spacer',
					'core/table',
					'core/video',
				];
			}
			return <BlockEdit { ...props } />;
		};
	}, 'omniformChildBlockRestrictions' )
);
