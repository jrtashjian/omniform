/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';

const Edit = ( props ) => {
	const {
		attributes,
		setAttributes,
	} = props;
	const {
		label,
	} = attributes;

	const blockProps = useBlockProps( {
		className: 'omniform-field-select-option',
	} );

	return (
		<li { ...blockProps }>
			<RichText
				aria-label={ __( 'Help text', 'omniform' ) }
				placeholder={ __( 'Write the option text…', 'omniform' ) }
				allowedFormats={ [] }
				withoutInteractiveFormatting
				value={ label }
				onChange={ ( html ) => setAttributes( { label: html } ) }
			/>
		</li>
	);
};
export default Edit;
