/**
 * WordPress dependencies
 */
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
			<Path fill="url(#a)" d="M17 11H7v2h10v-2Z" />
			<Path fill="url(#b)" fillRule="evenodd"
				d="M2 8a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V8Zm2 0h16v8H4V8Z" clipRule="evenodd" />
			<defs>
				<linearGradient id="a" x1="0" x2="28.328" y1="-2.4" y2="21.207" gradientUnits="userSpaceOnUse">
					<stop stopColor="#842ED9" />
					<stop offset=".499" stopColor="#D92E83" />
					<stop offset="1" stopColor="#FFD500" />
				</linearGradient>
				<linearGradient id="b" x1="0" x2="28.328" y1="-2.4" y2="21.207" gradientUnits="userSpaceOnUse">
					<stop stopColor="#842ED9" />
					<stop offset=".499" stopColor="#D92E83" />
					<stop offset="1" stopColor="#FFD500" />
				</linearGradient>
			</defs>
		</SVG>
	),
	example: {},
	merge( attributes, attributesToMerge ) {
		return {
			text:
				( attributes.text || '' ) +
				( attributesToMerge.text || '' ),
		};
	},
} );
