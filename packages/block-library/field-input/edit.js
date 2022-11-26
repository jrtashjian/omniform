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
		type,
		help,
		placeholder,
	} = attributes;

	const blockProps = useBlockProps();

	return (
		<div
			{ ...blockProps }
			className={ classNames( blockProps.className, `omniform-field-${ type }` ) }
		>
			<FormLabel originBlockProps={ props } />

			{ ( type === 'checkbox' || type === 'radio' ) ? (
				<div className="omniform-field-control" />
			) : (
				<RichText
					className="omniform-field-control placeholder-text"
					aria-label={ __( 'Help text', 'omniform' ) }
					placeholder={
						( placeholder || ! isSelected ) ? undefined : __( 'Enter a placeholder…', 'omniform' )
					}
					allowedFormats={ [] }
					withoutInteractiveFormatting
					value={ placeholder }
					onChange={ ( html ) => setAttributes( { placeholder: html } ) }
				/>
			) }

			{ ( isSelected || help ) && (
				<RichText
					className="omniform-field-support"
					tagName="p"
					aria-label={ __( 'Help text', 'omniform' ) }
					placeholder={ __( 'Write a help text…', 'omniform' ) }
					withoutInteractiveFormatting
					value={ help }
					onChange={ ( html ) => setAttributes( { help: html } ) }
				/>
			) }
		</div>
	);
};
export default Edit;
