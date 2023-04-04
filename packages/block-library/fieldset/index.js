/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { createBlock, registerBlockType } from '@wordpress/blocks';
import { decodeEntities } from '@wordpress/html-entities';
import { cleanForSlug } from '@wordpress/url';

/**
 * Internal dependencies
 */
import json from './block.json';
import Edit from './edit';
import Save from './save';
// import variations from './variations';
import { fieldGroup } from '../shared/icons';

import './style.scss';

const { name } = json;

registerBlockType( name, {
	edit: Edit,
	icon: { foreground: '#D92E83', src: fieldGroup },
	save: Save,
	// variations,
	example: {
		attributes: {
			fieldLabel: __( 'Field Group', 'omniform' ),
		},
		innerBlocks: [
			{
				attributes: {
					fieldLabel: __( 'Option One', 'omniform' ),
					layout: { type: 'flex', orientation: 'horizontal', justifyContent: 'left' },
				},
				name: 'omniform/field',
				innerBlocks: [
					{ name: 'omniform/input', attributes: { fieldType: 'radio' } },
					{ name: 'omniform/label' },
				],
			},
			{
				attributes: {
					fieldLabel: __( 'Option Two', 'omniform' ),
					layout: { type: 'flex', orientation: 'horizontal', justifyContent: 'left' },
				},
				name: 'omniform/field',
				innerBlocks: [
					{ name: 'omniform/input', attributes: { fieldType: 'radio' } },
					{ name: 'omniform/label' },
				],
			},
			{
				attributes: {
					fieldLabel: __( 'Option Three', 'omniform' ),
					layout: { type: 'flex', orientation: 'horizontal', justifyContent: 'left' },
				},
				name: 'omniform/field',
				innerBlocks: [
					{ name: 'omniform/input', attributes: { fieldType: 'radio' } },
					{ name: 'omniform/label' },
				],
			},
		],
	},
	// Get block name from the option value.
	__experimentalLabel: ( { fieldLabel } ) => fieldLabel && decodeEntities( fieldLabel ),

	// transforms: {
	// 	from: [
	// 		{
	// 			type: 'block',
	// 			blocks: [ 'omniform/field-select' ],
	// 			transform: ( { fieldLabel, fieldName, isMultiple }, innerBlocks ) => {
	// 				const options = [];

	// 				const getOptionLabels = ( block ) => {
	// 					if ( ! block.innerBlocks || block.innerBlocks.length === 0 ) {
	// 						options.push( block.attributes.fieldLabel );
	// 					}
	// 					block.innerBlocks.forEach( getOptionLabels );
	// 				};
	// 				innerBlocks.forEach( getOptionLabels );

	// 				return createBlock(
	// 					name,
	// 					{ fieldLabel, fieldName },
	// 					options.map( ( label ) => createBlock( 'omniform/field-input', {
	// 						fieldType: isMultiple ? 'checkbox' : 'radio',
	// 						fieldLabel: label,
	// 						fieldValue: cleanForSlug( label ),
	// 					} ) )
	// 				);
	// 			},
	// 		},
	// 	],
	// },
} );
