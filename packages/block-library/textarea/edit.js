/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';

const Edit = ( {
	attributes: { fieldPlaceholder },
	setAttributes,
	isSelected,
} ) => {
	const blockProps = useBlockProps();

	return (
		<RichText
			{ ...blockProps }
			identifier="fieldControl"
			aria-label={ __( 'Placeholder text for text input.', 'omniform' ) }
			placeholder={
				( fieldPlaceholder || ! isSelected )
					? undefined
					: __( 'Enter a placeholder…', 'omniform' )
			}
			value={ fieldPlaceholder }
			onChange={ ( html ) => setAttributes( { fieldPlaceholder: html } ) }
			withoutInteractiveFormatting
			allowedFormats={ [] }
		/>
	);
};

export default Edit;
