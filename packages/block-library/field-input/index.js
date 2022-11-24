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
import variations from './variations';

import './style.scss';
import './index.scss';

const { name } = json;

import { SVG, Path } from '@wordpress/primitives';

registerBlockType( name, {
	edit: Edit,
	icon: (
		<SVG viewBox="0 0 24 24">
			<Path fill="url(#a)" d="M14 11H6v2h8v-2Z" />
			<Path fill="url(#b)" fillRule="evenodd"
				d="M2 9a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V9Zm2 0h16v6H4V9Z" clipRule="evenodd" />
			<defs>
				<linearGradient id="a" x1="0" x2="24" y1="0" y2="24" gradientUnits="userSpaceOnUse">
					<stop stopColor="#842ED9" />
					<stop offset=".499" stopColor="#D92E83" />
					<stop offset="1" stopColor="#FFD500" />
				</linearGradient>
				<linearGradient id="b" x1="0" x2="24" y1="0" y2="24" gradientUnits="userSpaceOnUse">
					<stop stopColor="#842ED9" />
					<stop offset=".499" stopColor="#D92E83" />
					<stop offset="1" stopColor="#FFD500" />
				</linearGradient>
			</defs>
		</SVG>
	),
	variations,
	example: {
		attributes: {
			label: __( 'Example input', 'omniform' ),
		},
	},
} );
