/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';
import { useMergeRefs } from '@wordpress/compose';

/**
 * Internal dependencies
 */
import { cleanFieldName } from '../shared/utils';
import useEnter from '../shared/hooks';

const Edit = ( {
	attributes: { fieldValue, fieldName },
	setAttributes,
	isSelected,
	clientId,
} ) => {
	const blockProps = useBlockProps( {
		ref: useMergeRefs( [ useEnter( clientId ) ] ),
	} );

	return (
		<div { ...blockProps }>
			<RichText
				ref={ useMergeRefs( [ useEnter( clientId ) ] ) }
				identifier="fieldName"
				placeholder={ __( 'Enter a name for the field…', 'omniform' ) }
				value={ fieldName }
				onChange={ ( newFieldName ) => {
					setAttributes( { fieldName: newFieldName } );
				} }
				onBlur={ () => {
					setAttributes( { fieldName: cleanFieldName( fieldName ?? '' ) } );
				} }
				withoutInteractiveFormatting
				allowedFormats={ [] }
				disableLineBreaks
			/>
			<RichText
				ref={ useMergeRefs( [ useEnter( clientId ) ] ) }
				identifier="fieldControl"
				aria-label={ __( 'Placeholder text for text input.', 'omniform' ) }
				placeholder={
					( isSelected || fieldValue ) ? __( 'Enter a value…', 'omniform' ) : undefined
				}
				value={ fieldValue }
				onChange={ ( html ) => setAttributes( { fieldValue: html } ) }
				withoutInteractiveFormatting
				allowedFormats={ [] }
				disableLineBreaks
			/>
		</div>
	);
};

export default Edit;
