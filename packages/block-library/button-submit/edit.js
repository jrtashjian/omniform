/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	RichText,
	useBlockProps,
	__experimentalUseColorProps as useColorProps,
	__experimentalGetElementClassName,
} from '@wordpress/block-editor';

const Edit = ( {
	attributes,
	setAttributes,
} ) => {
	const {
		className,
		text,
	} = attributes;

	const colorProps = useColorProps( attributes );
	const blockProps = useBlockProps();

	return (
		<div { ...blockProps }>
			<RichText
				className={ classnames(
					className,
					'wp-block-button__link',
					colorProps.className,
					__experimentalGetElementClassName( 'button' ),
				) }
				style={ {
					...colorProps.style,
				} }
				tagName="button"
				type="submit"
				aria-label={ __( 'Button text', 'inquirywp' ) }
				placeholder={ __( 'Add text…', 'inquirywp' ) }
				withoutInteractiveFormatting
				value={ text }
				onChange={ ( html ) => setAttributes( { text: html } ) }
			/>
		</div>
	);
};
export default Edit;
