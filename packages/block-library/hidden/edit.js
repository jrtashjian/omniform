/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';
import { cleanForSlug } from '@wordpress/url';

const Edit = ( {
	attributes: { fieldValue, fieldName },
	setAttributes,
	isSelected,
} ) => {
	const blockProps = useBlockProps();

	return (
		<div { ...blockProps }>
			<RichText
				identifier="fieldLabel"
				placeholder={ __( 'Enter a label for the field…', 'omniform' ) }
				value={ fieldName }
				onChange={ ( newFieldName ) => {
					setAttributes( { fieldName: newFieldName } );
				} }
				onBlur={ () => {
					setAttributes( { fieldName: cleanForSlug( ( fieldName ).replace( /(<([^>]+)>)/gi, '' ) ) } );
				} }
				withoutInteractiveFormatting
				allowedFormats={ [] }
				style={ { fontSize: '0.8em', fontStyle: 'italic', paddingBottom: '0.5em' } }
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
			/>
		</div>
	);
};

export default Edit;
