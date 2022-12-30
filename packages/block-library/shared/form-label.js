/**
 * External dependencies
 */
import { kebabCase } from 'lodash';

/**
 * WordPress dependencies
 */

import { RichText } from '@wordpress/block-editor';
import { createBlock, getDefaultBlockName } from '@wordpress/blocks';
import { useEntityProp } from '@wordpress/core-data';
import { __ } from '@wordpress/i18n';

const FormLabel = ( { originBlockProps, isGrouped, isOptionInput, isRadioInput } ) => {
	const { postId: contextPostId, postType: contextPostType } = originBlockProps.context;

	const [ meta, setMeta ] = useEntityProp( 'postType', contextPostType, 'meta', contextPostId );

	const metaRequiredLabel = meta?.required_label;
	const updateMetaRequiredLabel = ( newValue ) => {
		setMeta( { ...meta, required_label: newValue } );
	};

	return (
		<div className="omniform-field-label">
			<RichText
				identifier="fieldLabel"
				tagName="span"
				placeholder={ __( 'Enter a label for the field…', 'omniform' ) }
				value={ originBlockProps.attributes.fieldLabel }
				onChange={ ( html ) => ! originBlockProps.attributes.fieldName || originBlockProps.attributes.fieldName === kebabCase( originBlockProps.attributes.fieldLabel )
					? originBlockProps.setAttributes( { fieldLabel: html, fieldName: kebabCase( html.replace( /(<([^>]+)>)/gi, '' ) ) } )
					: originBlockProps.setAttributes( { fieldLabel: html } )
				}
				// When hitting enter, place a new insertion point. This makes adding field a lot easier.
				onSplit={ ( value, isOriginal ) => {
					let block;

					if ( isOriginal || value ) {
						block = createBlock( originBlockProps.name, {
							...originBlockProps.attributes,
							fieldLabel: value,
						} );
					} else {
						block = isGrouped && isOptionInput
							? createBlock( originBlockProps.name, { fieldType: originBlockProps.attributes.fieldType } )
							: createBlock( getDefaultBlockName() );
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
			{ originBlockProps.attributes.isRequired && ( ! isRadioInput || ( isRadioInput && ! isGrouped ) ) && (
				<RichText
					identifier="requiredLabel"
					tagName="span"
					className="omniform-field-required"
					placeholder={ __( 'Enter a required field label…', 'omniform' ) }
					value={ metaRequiredLabel }
					onChange={ updateMetaRequiredLabel }
				/>
			) }
		</div>
	);
};
export default FormLabel;
