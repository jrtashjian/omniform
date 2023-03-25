/**
 * External dependencies
 */
import classNames from 'classnames';
import { kebabCase } from 'lodash';

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
	__unstableLayoutClassNames: layoutClassNames,
} ) => {
	const {
		fieldLabel,
		fieldName,
	} = attributes;

	const blockProps = useBlockProps( { className: layoutClassNames } );

	const innerBlockProps = useInnerBlocksProps();

	return (
		<div
			{ ...blockProps }
			className={ classNames( blockProps.className, 'omniform-fieldset' ) }
		>
			<RichText
				className="omniform-field-label"
				aria-label={ __( 'Legend text', 'omniform' ) }
				placeholder={ __( 'Enter a title to the field…', 'omniform' ) }
				multiple={ false }
				value={ fieldLabel }
				onChange={ ( html ) => ! fieldName || fieldName === kebabCase( fieldLabel.replace( /(<([^>]+)>)/gi, '' ) )
					? setAttributes( { fieldLabel: html, fieldName: kebabCase( html.replace( /(<([^>]+)>)/gi, '' ) ) } )
					: setAttributes( { fieldLabel: html } )
				}
				withoutInteractiveFormatting
				allowedFormats={ [ 'core/bold', 'core/italic', 'core/image' ] }
			/>
			{ innerBlockProps.children }
		</div>
	);
};
export default Edit;
