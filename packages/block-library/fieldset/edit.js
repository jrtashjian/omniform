/**
 * External dependencies
 */
import classNames from 'classnames';
import { kebabCase } from 'lodash';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';
import {
	InnerBlocks,
	RichText,
	useBlockProps,
	useInnerBlocksProps,
	store as blockEditorStore,
} from '@wordpress/block-editor';

const Edit = ( {
	attributes,
	setAttributes,
	isSelected,
	clientId,
} ) => {
	const {
		fieldLabel,
		fieldName,
	} = attributes;

	const hasSelectedInnerBlock = useSelect(
		( select ) => select( blockEditorStore ).hasSelectedInnerBlock( clientId, true ),
		[ clientId ]
	);

	const blockProps = useBlockProps();

	const innerBlockProps = useInnerBlocksProps( {}, {
		renderAppender: ( isSelected || hasSelectedInnerBlock ) && InnerBlocks.ButtonBlockAppender,
	} );

	return (
		<div
			{ ...blockProps }
			className={ classNames( blockProps.className, 'omniform-fieldset' ) }
		>
			<RichText
				className="omniform-field-label"
				aria-label={ __( 'Legend text', 'omniform' ) }
				placeholder={ __( 'Enter a title to the field…', 'omniform' ) }
				withoutInteractiveFormatting
				multiple={ false }
				value={ fieldLabel }
				onChange={ ( html ) => ! fieldName || fieldName === kebabCase( fieldLabel )
					? setAttributes( { fieldLabel: html, fieldName: kebabCase( html ) } )
					: setAttributes( { fieldLabel: html } )
				}
			/>
			{ innerBlockProps.children }
		</div>
	);
};
export default Edit;
