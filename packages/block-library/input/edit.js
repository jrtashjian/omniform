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
import useEnter from '../shared/hooks';

const Edit = ( {
	attributes: { fieldPlaceholder, fieldType },
	setAttributes,
	clientId,
	isSelected,
} ) => {
	const isTextInput = [ 'text', 'email', 'url', 'number', 'month', 'password', 'search', 'tel', 'week', 'hidden' ].includes( fieldType );
	const isOptionInput = [ 'checkbox', 'radio' ].includes( fieldType );

	const blockProps = useBlockProps( {
		ref: useMergeRefs( [ useEnter( clientId ) ] ),
	} );

	if ( isTextInput ) {
		return (
			<RichText
				{ ...blockProps }
				identifier="fieldControl"
				aria-label={ __( 'Placeholder text for text input.', 'omniform' ) }
				placeholder={
					( isSelected || fieldPlaceholder ) ? __( 'Enter a placeholder…', 'omniform' ) : undefined
				}
				value={ fieldPlaceholder }
				onChange={ ( html ) => setAttributes( { fieldPlaceholder: html } ) }
				withoutInteractiveFormatting
				allowedFormats={ [] }
				disableLineBreaks
			/>
		);
	}

	if ( isOptionInput ) {
		return ( <div type={ fieldType } { ...blockProps } /> );
	}

	return (
		<input type={ fieldType } { ...blockProps } readOnly />
	);
};

export default Edit;
