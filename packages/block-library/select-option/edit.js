/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';
import { createBlock } from '@wordpress/blocks';

const Edit = ( props ) => {
	const {
		attributes,
		setAttributes,
		mergeBlocks,
		onReplace,
		onRemove,
		clientId,
	} = props;
	const {
		fieldLabel,
	} = attributes;

	const blockProps = useBlockProps( {
		className: 'omniform-field-select-option',
	} );

	return (
		<div { ...blockProps }>
			<RichText
				identifier="fieldLabel"
				aria-label={ __( 'Help text', 'omniform' ) }
				placeholder={ __( 'Write the option text…', 'omniform' ) }
				value={ fieldLabel }
				onChange={ ( html ) => setAttributes( { fieldLabel: html } ) }
				withoutInteractiveFormatting
				allowedFormats={ [] }
				onSplit={ ( value, isOriginal ) => {
					let newAttributes;

					if ( isOriginal || value ) {
						newAttributes = {
							...attributes,
							fieldLabel: value.trim(),
						};
					}

					const block = createBlock( props.name, newAttributes );

					if ( isOriginal ) {
						block.clientId = clientId;
					}

					return block;
				} }
				onMerge={ mergeBlocks }
				onReplace={ onReplace }
				onRemove={ onRemove }
				__unstableAllowPrefixTransformations
			/>
		</div>
	);
};
export default Edit;
