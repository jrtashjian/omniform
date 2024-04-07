/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	RichText,
	store as blockEditorStore,
	useBlockProps,
} from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import { cleanForSlug } from '@wordpress/url';

/**
 * Internal dependencies
 */
import {
	onMerge,
	onReplace,
	onSplit,
} from '../shared/rich-text-handlers';

const Edit = ( {
	attributes: { fieldValue, fieldName },
	setAttributes,
	isSelected,
	clientId,
} ) => {
	const { getBlock } = useSelect( blockEditorStore );

	const blockProps = useBlockProps();

	return (
		<div { ...blockProps }>
			<RichText
				identifier="fieldName"
				placeholder={ __( 'Enter a name for the field…', 'omniform' ) }
				value={ fieldName }
				onChange={ ( newFieldName ) => {
					setAttributes( { fieldName: newFieldName } );
				} }
				onBlur={ () => {
					setAttributes( { fieldName: cleanForSlug( ( fieldName ?? '' ).replace( /(<([^>]+)>)/gi, '' ) ) } );
				} }
				withoutInteractiveFormatting
				allowedFormats={ [] }

				onSplit={ ( ...args ) => onSplit( getBlock( clientId ), ...args ) }
				onReplace={ ( ...args ) => onReplace( getBlock( clientId ), ...args ) }
				onMerge={ ( ...args ) => onMerge( getBlock( clientId ), ...args ) }
			/>
			<RichText
				identifier="fieldControl"
				aria-label={ __( 'Placeholder text for text input.', 'omniform' ) }
				placeholder={
					( isSelected || fieldValue ) ? __( 'Enter a value…', 'omniform' ) : undefined
				}
				value={ fieldValue }
				onChange={ ( html ) => setAttributes( { fieldValue: html } ) }
				withoutInteractiveFormatting
				allowedFormats={ [] }

				onSplit={ ( ...args ) => onSplit( getBlock( clientId ), ...args ) }
				onReplace={ ( ...args ) => onReplace( getBlock( clientId ), ...args ) }
				onMerge={ ( ...args ) => onMerge( getBlock( clientId ), ...args ) }
			/>
		</div>
	);
};

export default Edit;
