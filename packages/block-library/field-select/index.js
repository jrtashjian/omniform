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
import variations from './variations';
import { fieldSelect } from '../shared/icons';

import './style.scss';
import './index.scss';

const { name } = json;

registerBlockType( name, {
	edit: Edit,
	save: Save,
	icon: { foreground: '#D92E83', src: fieldSelect },
	example: {
		attributes: {
			fieldLabel: __( 'Example input', 'omniform' ),
		},
	},
	variations,
	merge: ( attributes, attributesToMerge ) => {
		return {
			fieldLabel:
				( attributes.fieldLabel || '' ) +
				( attributesToMerge.fieldLabel || '' ),
		};
	},
	// Get block name from the option value.
	__experimentalLabel: ( { fieldLabel } ) => fieldLabel && decodeEntities( fieldLabel ),

	transforms: {
		from: [
			{
				type: 'block',
				isMultiBlock: true,
				blocks: [ 'omniform/field-input' ],
				isMatch: ( inputs ) => inputs.every( ( { fieldType } ) => fieldType === 'radio' ),
				transform: ( options ) => createBlock( name, {},
					options.map( ( { fieldLabel } ) =>
						createBlock( 'omniform/select-option', { fieldLabel } )
					)
				),
			},
			{
				type: 'block',
				isMultiBlock: true,
				blocks: [ 'omniform/field-input' ],
				isMatch: ( inputs ) => inputs.every( ( { fieldType } ) => fieldType === 'checkbox' ),
				transform: ( options ) => createBlock( name, { isMultiple: true },
					options.map( ( { fieldLabel } ) =>
						createBlock( 'omniform/select-option', { fieldLabel } )
					)
				),
			},
		],
	},
} );
