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
		label,
	} = attributes;

	const blockProps = useBlockProps( {
		className: 'omniform-field-select-option',
	} );

	return (
		<RichText
			{ ...blockProps }
			identifier="label"
			tagName="li"
			aria-label={ __( 'Help text', 'omniform' ) }
			placeholder={ __( 'Write the option text…', 'omniform' ) }
			allowedFormats={ [] }
			withoutInteractiveFormatting
			value={ label }
			onChange={ ( html ) => setAttributes( { label: html } ) }
			onSplit={ ( value, isOriginal ) => {
				let newAttributes;

				if ( isOriginal || value ) {
					newAttributes = {
						...attributes,
						label: value.trim(),
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
		/>
	);
};
export default Edit;
