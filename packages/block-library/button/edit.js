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
	__experimentalUseBorderProps as useBorderProps,
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

	const borderProps = useBorderProps( attributes );
	const colorProps = useColorProps( attributes );
	const blockProps = useBlockProps();

	return (
		<div
			{ ...blockProps }
			// Prevent color supports on wrapper.
			className={
				blockProps.className
					.replace( colorProps.className, '' )
					.replace( borderProps.className, '' )
			}
			style={ null }
		>
			<RichText
				identifier="buttonLabel"
				className={ classnames(
					className,
					colorProps.className,
					borderProps.className,
					__experimentalGetElementClassName( 'button' ),
					'wp-block-button__link',
				) }
				style={ {
					...colorProps.style,
					...borderProps.style,
				} }
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
