/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { createBlock, getDefaultBlockName } from '@wordpress/blocks';
import {
	RichText,
	useBlockProps,
	__experimentalUseColorProps as useColorProps,
	__experimentalGetElementClassName,
} from '@wordpress/block-editor';

const Edit = ( {
	attributes,
	setAttributes,
	onReplace,
	mergeBlocks,
	clientId,
} ) => {
	const {
		className,
		buttonLabel,
	} = attributes;

	const colorProps = useColorProps( attributes );
	const blockProps = useBlockProps();

	return (
		<div { ...blockProps }>
			<RichText
				identifier="buttonLabel"
				className={ classnames(
					className,
					colorProps.className,
					__experimentalGetElementClassName( 'button' ),
				) }
				style={ {
					...colorProps.style,
				} }
				tagName="button"
				aria-label={ __( 'Button text', 'omniform' ) }
				placeholder={ __( 'Add text…', 'omniform' ) }
				value={ buttonLabel }
				withoutInteractiveFormatting
				onChange={ ( value ) => setAttributes( { buttonLabel: value } ) }
				onSplit={ ( value, isOriginal ) => {
					let block;

					if ( isOriginal || value ) {
						block = createBlock( 'omniform/button', {
							...attributes,
							buttonLabel: value,
						} );
					} else {
						block = createBlock(
							getDefaultBlockName() ?? 'omniform/button'
						);
					}

					if ( isOriginal ) {
						block.clientId = clientId;
					}

					return block;
				} }
				onReplace={ onReplace }
				onRemove={ () => onReplace( [] ) }
				onMerge={ mergeBlocks }
			/>
		</div>
	);
};
export default Edit;
