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

import './style.scss';
import './index.scss';

const { name } = json;

import { SVG, Path } from '@wordpress/primitives';

registerBlockType( name, {
	edit,
	icon: (
		<SVG viewBox="0 0 24 24">
			<Path fill="url(#a)" fillRule="evenodd"
				d="M2.556 13.556a2.2 2.2 0 0 1 0-3.112L4.3 8.7V6.5a2.2 2.2 0 0 1 2.2-2.2h2.2l1.634-1.634a2.2 2.2 0 0 1 3.111 0L15.08 4.3H17.5a2.2 2.2 0 0 1 2.2 2.2v2.421l1.523 1.523a2.2 2.2 0 0 1 0 3.112L19.7 15.079V17.5a2.2 2.2 0 0 1-2.2 2.2h-2.421l-1.634 1.634a2.2 2.2 0 0 1-3.111 0L8.7 19.7H6.5a2.2 2.2 0 0 1-2.2-2.2v-2.2l-1.744-1.744ZM12 5a7 7 0 0 1 7 7h-1.604A5.396 5.396 0 0 0 12 6.604V5Zm0 12.396V19a7 7 0 0 1-7-7h1.604A5.396 5.396 0 0 0 12 17.396ZM15.5 12A3.5 3.5 0 0 0 12 8.5v1.896c.886 0 1.604.718 1.604 1.604H15.5Zm-5.104 0c0 .886.718 1.604 1.604 1.604V15.5A3.5 3.5 0 0 1 8.5 12h1.896Z"
				clipRule="evenodd" />
			<defs>
				<linearGradient id="a" x1="-.084" x2="38.232" y1="-11.948" y2="7.21" gradientUnits="userSpaceOnUse">
					<stop stopColor="#842ED9" />
					<stop offset=".499" stopColor="#D92E83" />
					<stop offset="1" stopColor="#FFD500" />
				</linearGradient>
			</defs>
		</SVG>
	),
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
