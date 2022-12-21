/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { createBlock, registerBlockType } from '@wordpress/blocks';
import { decodeEntities } from '@wordpress/html-entities';

/**
 * Internal dependencies
 */
import json from './block.json';
import Edit from './edit';
import Save from './save';
import { fieldGroup } from '../shared/icons';

import './style.scss';
import './index.scss';

const { name } = json;

registerBlockType( name, {
	edit: Edit,
	icon: { foreground: '#D92E83', src: fieldGroup },
	save: Save,
	example: {
		attributes: {
			fieldLabel: __( 'Field Group', 'omniform' ),
		},
		innerBlocks: [
			{
				attributes: {
					fieldType: 'radio',
					fieldLabel: __( 'Option One', 'omniform' ),
				},
				name: 'omniform/field-input',
			},
			{
				attributes: {
					fieldType: 'radio',
					fieldLabel: __( 'Option Two', 'omniform' ),
				},
				name: 'omniform/field-input',
			},
			{
				attributes: {
					fieldType: 'radio',
					fieldLabel: __( 'Option Three', 'omniform' ),
				},
				name: 'omniform/field-input',
			},
		],
	},
	// Get block name from the option value.
	__experimentalLabel: ( { fieldLabel } ) => fieldLabel && decodeEntities( fieldLabel ),

	transforms: {
		from: [
			{
				type: 'block',
				blocks: [ 'omniform/field-select' ],
				transform: ( { fieldLabel, fieldName }, innerBlocks ) => {
					const options = [];

					const getOptionLabels = ( block ) => {
						if ( ! block.innerBlocks || block.innerBlocks.length === 0 ) {
							options.push( block.attributes.fieldLabel );
						}
						block.innerBlocks.forEach( getOptionLabels );
					};
					innerBlocks.forEach( getOptionLabels );

					return createBlock(
						name,
						{ fieldLabel, fieldName },
						options.map( ( label ) => createBlock( 'omniform/field-input', { fieldType: 'radio', fieldLabel: label } ) )
					);
				},
			},
		],
	},
} );
