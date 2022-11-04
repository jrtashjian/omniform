/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import { useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
import json from './block.json';
import Edit from './edit';

import './style.scss';
import './index.scss';

const { name } = json;

registerBlockType( name, {
	edit: Edit,
	// Get block name from the post name.
	__experimentalLabel: ( attributes ) => {
		const { ref } = attributes;

		const { value } = useSelect(
			( select ) => {
				const { getEntityRecord, getEditedEntityRecord } = select( 'core' );

				const record = getEntityRecord( 'postType', 'inquirywp_form', ref );
				const editedRecord = getEditedEntityRecord( 'postType', 'inquirywp_form', ref );

				return record && editedRecord
					? { value: editedRecord.title }
					: {};
			},
			[ ref ]
		);

		return value;
	},
} );
