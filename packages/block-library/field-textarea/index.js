/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import json from './block.json';
import Edit from './edit';

import './style.scss';
import './index.scss';

const { name } = json;

import { SVG, Path } from '@wordpress/primitives';

registerBlockType( name, {
	edit: Edit,
	icon: (
		<SVG viewBox="0 0 24 24">
			<Path fill="url(#a)" d="M18 9H6v2h12V9Z" />
			<Path fill="url(#b)" d="M6 13h8v2H6v-2Z" />
			<Path fill="url(#c)" fillRule="evenodd"
				d="M4 5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H4Zm16 2H4v10h16V7Z"
				clipRule="evenodd" />
			<defs>
				<linearGradient id="a" x1="0" x2="31.784" y1="-4.8" y2="17.903" gradientUnits="userSpaceOnUse">
					<stop stopColor="#842ED9" />
					<stop offset=".499" stopColor="#D92E83" />
					<stop offset="1" stopColor="#FFD500" />
				</linearGradient>
				<linearGradient id="b" x1="0" x2="31.784" y1="-4.8" y2="17.903" gradientUnits="userSpaceOnUse">
					<stop stopColor="#842ED9" />
					<stop offset=".499" stopColor="#D92E83" />
					<stop offset="1" stopColor="#FFD500" />
				</linearGradient>
				<linearGradient id="c" x1="0" x2="31.784" y1="-4.8" y2="17.903" gradientUnits="userSpaceOnUse">
					<stop stopColor="#842ED9" />
					<stop offset=".499" stopColor="#D92E83" />
					<stop offset="1" stopColor="#FFD500" />
				</linearGradient>
			</defs>
		</SVG>
	),
	example: {
		attributes: {
			label: __( 'Example input', 'omniform' ),
		},
	},
} );
