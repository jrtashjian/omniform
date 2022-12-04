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
	useInnerBlocksProps,
} from '@wordpress/block-editor';

const Edit = ( {
	attributes,
	setAttributes,
} ) => {
	const {
		fieldLabel,
	} = attributes;

	const blockProps = useBlockProps();
	const innerBlockProps = useInnerBlocksProps();

	return (
		<div
			{ ...blockProps }
			className={ classNames( blockProps.className, 'omniform-fieldset is-layout-flow' ) }
		>
			<RichText
				className="omniform-field-label"
				tagName="legend"
				aria-label={ __( 'Legend text', 'omniform' ) }
				placeholder={ __( 'Enter a title to the field…', 'omniform' ) }
				withoutInteractiveFormatting
				multiple={ false }
				value={ fieldLabel }
				onChange={ ( html ) => setAttributes( { fieldLabel: html } ) }
			/>
			{ innerBlockProps.children }
		</div>
	);
};
export default Edit;
