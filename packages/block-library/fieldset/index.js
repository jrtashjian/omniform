/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { decodeEntities } from '@wordpress/html-entities';

/**
 * Internal dependencies
 */
import json from './block.json';
import Edit from './edit';
import Save from './save';
import { fieldGroup as icon } from '../shared/icons';

import './style.scss';
import './index.scss';

const { name } = json;

registerBlockType( name, {
	edit: Edit,
	icon,
	save: Save,
	example: {
		attributes: {
			fieldLabel: __( 'Field Group', 'omniform' ),
		},
		innerBlocks: [
			{
				attributes: {
					fieldType: 'checkbox',
					fieldLabel: __( 'Option One', 'omniform' ),
				},
				name: 'omniform/field-input',
			},
			{
				attributes: {
					fieldType: 'checkbox',
					fieldLabel: __( 'Option Two', 'omniform' ),
				},
				name: 'omniform/field-input',
			},
			{
				attributes: {
					fieldType: 'checkbox',
					fieldLabel: __( 'Option Three', 'omniform' ),
				},
				name: 'omniform/field-input',
			},
		],
	},
	usesContext: [ 'omniform/fieldGroupName' ],
	// Get block name from the option value.
	__experimentalLabel: ( { fieldLabel } ) => fieldLabel && decodeEntities( fieldLabel ),
} );
