/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	RichText,
	store as blockEditorStore,
	useBlockProps,
} from '@wordpress/block-editor';
import { useSelect, useDispatch } from '@wordpress/data';
import { cloneBlock, createBlock } from '@wordpress/blocks';

const Edit = ( {
	attributes: { fieldPlaceholder, fieldType, fieldValue },
	setAttributes,
	clientId,
	isSelected,
} ) => {
	const {
		getBlock,
		getBlockRootClientId,
	} = useSelect( blockEditorStore );

	const {
		replaceBlocks,
		selectBlock,
	} = useDispatch( blockEditorStore );

	const isTextInput = [ 'text', 'email', 'url', 'number', 'month', 'password', 'search', 'tel', 'week', 'hidden' ].includes( fieldType );
	const isOptionInput = [ 'checkbox', 'radio' ].includes( fieldType );
	const isHiddenInput = fieldType === 'hidden';

	const richTextPlaceholder = isHiddenInput
		? __( 'Enter a value…', 'omniform' )
		: __( 'Enter a placeholder…', 'omniform' );
	const richTextOnChange = ( html ) => isHiddenInput
		? setAttributes( { fieldValue: html } )
		: setAttributes( { fieldPlaceholder: html } );

	/**
	 * Handles splitting the parent field block.
	 *
	 * @param {string}  value      The value of the field label.
	 * @param {boolean} isOriginal Whether the field label is the original.
	 * @return {Object} The new block.
	 */
	const onSplit = ( value, isOriginal ) => {
		let block;

		const rootClientId = getBlockRootClientId( clientId );
		const rootBlock = getBlock( rootClientId );

		/**
		 * Prepares the inner blocks of the new field block.
		 *
		 * @param {Array} innerBlocks The inner blocks of the parent field block.
		 * @return {Array} The prepared inner blocks.
		 */
		const prepareInnerBlocks = ( innerBlocks ) => {
			return innerBlocks.map(
				( { name, attributes } ) => 'omniform/input' === name
					? createBlock( name, { ...attributes, fieldPlaceholder: undefined, fieldValue: undefined } )
					: createBlock( name, { style: attributes.style } )
			);
		};

		if ( isOriginal ) {
			block = cloneBlock(
				rootBlock,
				rootBlock.attributes,
				! isOriginal && prepareInnerBlocks( rootBlock.innerBlocks )
			);
		} else {
			block = createBlock(
				'omniform/field',
				{
					...rootBlock.attributes,
					fieldLabel: '',
					fieldName: '',
				},
				prepareInnerBlocks( rootBlock.innerBlocks )
			);
		}

		if ( isOriginal ) {
			block.clientId = rootClientId;
		}

		return block;
	};

	/**
	 * Handles replacing the parent field block.
	 *
	 * @param {Array}  blocks          The blocks to replace the parent field block with.
	 * @param {number} indexToSelect   The index of the block to select.
	 * @param {number} initialPosition The initial position of the caret.
	 */
	const onReplace = ( blocks, indexToSelect, initialPosition ) => {
		replaceBlocks(
			[ getBlockRootClientId( clientId ) ],
			blocks,
			indexToSelect,
			initialPosition
		);

		// focus the label of the last field block inserted
		selectLabelBlock( blocks[ indexToSelect ].clientId, -1 );
	};

	/**
	 * Selects the label block of a field block.
	 *
	 * @param {string} fieldClientId   The client ID of the field block.
	 * @param {number} initialPosition The initial position of the caret.
	 */
	const selectLabelBlock = ( fieldClientId, initialPosition = 0 ) => {
		const labelBlock = getBlock( fieldClientId ).innerBlocks.filter(
			( block ) => block.name === 'omniform/label'
		)[ 0 ];

		selectBlock( labelBlock.clientId, initialPosition );
	};

	const blockProps = useBlockProps();

	if ( isTextInput ) {
		return (
			<RichText
				{ ...blockProps }
				identifier="fieldControl"
				aria-label={ __( 'Placeholder text for text input.', 'omniform' ) }
				placeholder={
					( isSelected || fieldPlaceholder ) ? richTextPlaceholder : undefined
				}
				value={ isHiddenInput ? fieldValue : fieldPlaceholder }
				onChange={ richTextOnChange }
				withoutInteractiveFormatting
				allowedFormats={ [] }

				onSplit={ onSplit }
				onReplace={ onReplace }
			/>
		);
	}

	if ( isOptionInput ) {
		return ( <div type={ fieldType } { ...blockProps } /> );
	}

	return (
		<input
			type={ fieldType }
			disabled
			{ ...blockProps }
		/>
	);
};

export default Edit;
