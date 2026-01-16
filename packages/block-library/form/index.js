/**
 * External dependencies
 */
import { capitalCase } from 'change-case';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { createHigherOrderComponent } from '@wordpress/compose';
import { registerBlockType, switchToBlockType } from '@wordpress/blocks';
import { dispatch, select } from '@wordpress/data';
import { decodeEntities } from '@wordpress/html-entities';
import { addFilter } from '@wordpress/hooks';
import {
	InnerBlocks,
	BlockControls,
} from '@wordpress/block-editor';
import { ToolbarButton, ToolbarGroup } from '@wordpress/components';

/**
 * Internal dependencies
 */
import json from './block.json';
import StandardForm from './edit/standard-form';
import StandaloneForm from './edit/standalone-form';
import transforms from './transforms';
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
	transforms,
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

// Show "Replace with OmniForm" in Ollie Pro Placeholder Forms.
addFilter(
	'editor.BlockEdit',
	'omniform/replace-with-omniform',
	createHigherOrderComponent( ( BlockEdit ) => {
		return ( props ) => {
			if ( props.name !== 'core/group' || props.attributes?.metadata?.name !== 'Sample Form' ) {
				return <BlockEdit { ...props } />;
			}

			const { getBlock } = select( 'core/block-editor' );
			const { replaceBlocks } = dispatch( 'core/block-editor' );

			const handleClick = () => {
				const newBlocks = switchToBlockType( getBlock( props.clientId ), 'omniform/form' );
				replaceBlocks( props.clientId, newBlocks );
			};

			return (
				<>
					<BlockControls>
						<ToolbarGroup>
							<ToolbarButton
								icon={ form }
								text={ __( 'Replace with OmniForm', 'omniform' ) }
								onClick={ handleClick }
							/>
						</ToolbarGroup>
					</BlockControls>
					<BlockEdit key="edit" { ...props } />
				</>
			);
		};
	}, 'replaceWithOmniForm' )
);

addFilter(
	'editor.BlockListBlock',
	'omniform/with-omniform-overlay',
	createHigherOrderComponent(
		( BlockListBlock ) => {
			return ( props ) => {
				if ( props.name !== 'core/group' || props.attributes?.metadata?.name !== 'Sample Form' ) {
					return <BlockListBlock { ...props } />;
				}

				return (
					<BlockListBlock { ...props } className="with-omniform-overlay" />
				);
			};
		},
		'withOmniFormOverlay'
	)
);
