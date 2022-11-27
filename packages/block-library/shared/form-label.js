/**
 * WordPress dependencies
 */

import { RichText } from '@wordpress/block-editor';
import { createBlock, getDefaultBlockName } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';

const FormLabel = ( { originBlockProps } ) => (
	<RichText
		className="omniform-field-label"
		placeholder={ __( 'Enter a label for the field…', 'omniform' ) }
		value={ originBlockProps.attributes.label }
		onChange={ ( html ) => originBlockProps.setAttributes( { label: html } ) }
		// When hitting enter, place a new insertion point. This makes adding field a lot easier.
		onSplit={ ( value, isOriginal ) => {
			let block;

			if ( isOriginal || value ) {
				block = createBlock( originBlockProps.name, {
					...originBlockProps.attributes,
					label: value,
				} );
			} else {
				block = createBlock( getDefaultBlockName() );
			}

			if ( isOriginal ) {
				block.clientId = originBlockProps.clientId;
			}

			return block;
		} }
		onMerge={ originBlockProps.mergeBlocks }
		onReplace={ originBlockProps.onReplace }
		onRemove={ originBlockProps.onRemove }
	/>
);
export default FormLabel;
