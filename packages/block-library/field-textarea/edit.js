/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';

/**
 * Internal dependencies
 */
import FormLabel from '../shared/form-label';

const Edit = ( props ) => {
	const {
		attributes,
		setAttributes,
		isSelected,
	} = props;
	const {
		fieldPlaceholder,
	} = attributes;

	const blockProps = useBlockProps();
	return (
		<div { ...blockProps }>
			<FormLabel originBlockProps={ props } />

			<RichText
				identifier="fieldControl"
				className="omniform-field-control"
				aria-label={ __( 'Placeholder text for text input.', 'omniform' ) }
				placeholder={
					( fieldPlaceholder || ! isSelected )
						? undefined
						: __( 'Enter a placeholder…', 'omniform' )
				}
				allowedFormats={ [] }
				withoutInteractiveFormatting
				value={ fieldPlaceholder }
				onChange={ ( html ) => setAttributes( { fieldPlaceholder: html } ) }
			/>
		</div>
	);
};
export default Edit;
