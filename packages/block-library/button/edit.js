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
		buttonLabel,
	} = attributes;

	const blockProps = useBlockProps( {
		className: classnames(
			__experimentalGetElementClassName( 'button' ),
			'wp-block-button__link',
		),
	} );

	return (
		<div>
			<RichText
				{ ...blockProps }
				identifier="buttonLabel"
				aria-label={ __( 'Button text', 'omniform' ) }
				placeholder={ __( 'Add text…', 'omniform' ) }
				value={ buttonLabel }
				onChange={ ( value ) => setAttributes( { buttonLabel: value } ) }
				preserveWhiteSpace
				withoutInteractiveFormatting
				allowedFormats={ [ 'core/bold', 'core/italic', 'core/image' ] }
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
