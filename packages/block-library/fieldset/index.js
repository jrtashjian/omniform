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
import Save from './save';

import './style.scss';
import './index.scss';

const { name } = json;

import { SVG, Path } from '@wordpress/primitives';

registerBlockType( name, {
	edit: Edit,
	icon: (
		<SVG viewBox="0 0 24 24">
			<Path fill="url(#a)" d="M7 7h2v2H7V7Z" />
			<Path fill="url(#b)" d="M9 11H7v2h2v-2Z" />
			<Path fill="url(#c)" d="M7 15h2v2H7v-2Z" />
			<Path fill="url(#d)" d="M17 11h-6v2h6v-2Z" />
			<Path fill="url(#e)" d="M11 15h6v2h-6v-2Z" />
			<Path fill="url(#f)" d="M17 7h-6v2h6V7Z" />
			<Path fill="url(#g)" fillRule="evenodd"
				d="M5 3a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2H5Zm14 2H5v14h14V5Z"
				clipRule="evenodd" />
			<defs>
				<linearGradient id="a" x1="1.2" x2="35.76" y1="-9.6" y2="7.68" gradientUnits="userSpaceOnUse">
					<stop stopColor="#842ED9" />
					<stop offset=".499" stopColor="#D92E83" />
					<stop offset="1" stopColor="#FFD500" />
				</linearGradient>
				<linearGradient id="b" x1="1.2" x2="35.76" y1="-9.6" y2="7.68" gradientUnits="userSpaceOnUse">
					<stop stopColor="#842ED9" />
					<stop offset=".499" stopColor="#D92E83" />
					<stop offset="1" stopColor="#FFD500" />
				</linearGradient>
				<linearGradient id="c" x1="1.2" x2="35.76" y1="-9.6" y2="7.68" gradientUnits="userSpaceOnUse">
					<stop stopColor="#842ED9" />
					<stop offset=".499" stopColor="#D92E83" />
					<stop offset="1" stopColor="#FFD500" />
				</linearGradient>
				<linearGradient id="d" x1="1.2" x2="35.76" y1="-9.6" y2="7.68" gradientUnits="userSpaceOnUse">
					<stop stopColor="#842ED9" />
					<stop offset=".499" stopColor="#D92E83" />
					<stop offset="1" stopColor="#FFD500" />
				</linearGradient>
				<linearGradient id="e" x1="1.2" x2="35.76" y1="-9.6" y2="7.68" gradientUnits="userSpaceOnUse">
					<stop stopColor="#842ED9" />
					<stop offset=".499" stopColor="#D92E83" />
					<stop offset="1" stopColor="#FFD500" />
				</linearGradient>
				<linearGradient id="f" x1="1.2" x2="35.76" y1="-9.6" y2="7.68" gradientUnits="userSpaceOnUse">
					<stop stopColor="#842ED9" />
					<stop offset=".499" stopColor="#D92E83" />
					<stop offset="1" stopColor="#FFD500" />
				</linearGradient>
				<linearGradient id="g" x1="1.2" x2="35.76" y1="-9.6" y2="7.68" gradientUnits="userSpaceOnUse">
					<stop stopColor="#842ED9" />
					<stop offset=".499" stopColor="#D92E83" />
					<stop offset="1" stopColor="#FFD500" />
				</linearGradient>
			</defs>
		</SVG>
	),
	save: Save,
	example: {
		attributes: {
			label: __( 'Field Group', 'omniform' ),
		},
		innerBlocks: [
			{
				attributes: {
					type: 'checkbox',
					label: __( 'Option One', 'omniform' ),
				},
				name: 'omniform/field-input',
			},
			{
				attributes: {
					type: 'checkbox',
					label: __( 'Option Two', 'omniform' ),
				},
				name: 'omniform/field-input',
			},
			{
				attributes: {
					type: 'checkbox',
					label: __( 'Option Three', 'omniform' ),
				},
				name: 'omniform/field-input',
			},
		],
	},
} );
