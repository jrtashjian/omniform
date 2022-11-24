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
			<Path fill="url(#a)" d="M6 7h8v2H6V7Z" />
			<Path fill="url(#b)" d="M14 11H6v2h8v-2Z" />
			<Path fill="url(#c)" d="M6 15h8v2H6v-2Z" />
			<Path fill="url(#d)" d="M18 7h-2v2h2V7Z" />
			<Path fill="url(#e)" fillRule="evenodd"
				d="M4 3a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12.5a1.5 1.5 0 0 0 1.5-1.5V13h2.5a1.5 1.5 0 0 0 1.5-1.5V5a2 2 0 0 0-2-2H4Zm16 8V5H4v14h12v-6.5a1.5 1.5 0 0 1 1.5-1.5H20Z"
				clipRule="evenodd" />
			<defs>
				<linearGradient id="a" x1="0" x2="36.679" y1="-9.6" y2="10.777" gradientUnits="userSpaceOnUse">
					<stop stopColor="#842ED9" />
					<stop offset=".499" stopColor="#D92E83" />
					<stop offset="1" stopColor="#FFD500" />
				</linearGradient>
				<linearGradient id="b" x1="0" x2="36.679" y1="-9.6" y2="10.777" gradientUnits="userSpaceOnUse">
					<stop stopColor="#842ED9" />
					<stop offset=".499" stopColor="#D92E83" />
					<stop offset="1" stopColor="#FFD500" />
				</linearGradient>
				<linearGradient id="c" x1="0" x2="36.679" y1="-9.6" y2="10.777" gradientUnits="userSpaceOnUse">
					<stop stopColor="#842ED9" />
					<stop offset=".499" stopColor="#D92E83" />
					<stop offset="1" stopColor="#FFD500" />
				</linearGradient>
				<linearGradient id="d" x1="0" x2="36.679" y1="-9.6" y2="10.777" gradientUnits="userSpaceOnUse">
					<stop stopColor="#842ED9" />
					<stop offset=".499" stopColor="#D92E83" />
					<stop offset="1" stopColor="#FFD500" />
				</linearGradient>
				<linearGradient id="e" x1="0" x2="36.679" y1="-9.6" y2="10.777" gradientUnits="userSpaceOnUse">
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
	variations,
} );
