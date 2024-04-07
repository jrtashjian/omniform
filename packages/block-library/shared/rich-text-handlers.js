/**
 * WordPress dependencies
 */
import {
	store as blockEditorStore,
} from '@wordpress/block-editor';
import {
	cloneBlock,
	createBlock,
	getDefaultBlockName,
} from '@wordpress/blocks';
import { select, dispatch } from '@wordpress/data';

/**
 * Handles splitting a block.
 *
 * @param {Object}  targetBlock The target block.
 * @param {string}  value       The value of the field label.
 * @param {boolean} isOriginal  Whether the field label is the original.
 *
 * @return {Object} The new block.
 */
export function onSplit( targetBlock, value, isOriginal ) {
	let block;

	if ( isOriginal ) {
		block = cloneBlock(
			targetBlock,
			targetBlock.attributes,
			targetBlock.innerBlocks
		);
	} else {
		block = createBlock(
			targetBlock.name,
			{ style: targetBlock.attributes.style },
			targetBlock.innerBlocks.map(
				( { name, attributes } ) => [ 'omniform/input', 'omniform/select', 'omniform/textarea' ].includes( name )
					? createBlock( name, { fieldPlaceholder: undefined, fieldValue: undefined, fieldType: attributes.fieldType, style: attributes.style } )
					: createBlock( name, { style: attributes.style } )
			)
		);
	}

	if ( isOriginal ) {
		block.clientId = targetBlock.clientId;
	}

	return block;
}

/**
 * Handles replacing the parent field block.
 *
 * @param {string} targetBlock     The target block.
 * @param {Array}  blocks          The blocks to replace the parent field block with.
 * @param {number} indexToSelect   The index of the block to select.
 * @param {number} initialPosition The initial position of the caret.
 */
export function onReplace( targetBlock, blocks, indexToSelect, initialPosition ) {
	const {
		replaceBlocks,
		selectBlock,
	} = dispatch( blockEditorStore );

	replaceBlocks(
		[ targetBlock.clientId ],
		blocks,
		indexToSelect,
		initialPosition
	);

	// Select the label block if it exists, otherwise select the input block.
	const labelBlock = blocks[ indexToSelect ]?.innerBlocks.find( ( block ) => block.name === 'omniform/label' );
	const inputBlock = blocks[ indexToSelect ]?.innerBlocks.find( ( block ) => [ 'omniform/input', 'omniform/textarea', 'omniform/select' ].includes( block.name ) );

	if ( labelBlock ) {
		selectBlock( labelBlock.clientId );
	} else if ( inputBlock ) {
		selectBlock( inputBlock.clientId );
	} else {
		selectBlock( blocks[ indexToSelect ].clientId );
	}
}

/**
 * Handles removing a block.
 *
 * @param {Object} targetBlock The target block.
 */
export function onRemove( targetBlock ) {
	const {
		removeBlock,
	} = dispatch( blockEditorStore );

	removeBlock( targetBlock.clientId );
}

/**
 * Handles merging a block with the next or previous block.
 *
 * @param {Object}  targetBlock The target block.
 * @param {boolean} forward     Whether to merge with the next block or the previous block.
 */
export function onMerge( targetBlock, forward ) {
	const {
		getNextBlockClientId,
		getPreviousBlockClientId,
	} = select( blockEditorStore );

	const {
		mergeBlocks,
		replaceBlock,
		selectBlock,
	} = dispatch( blockEditorStore );

	// If the target block is empty, replace it with a default block.
	if (
		( targetBlock.name === 'omniform/field' && ! targetBlock.attributes?.fieldLabel ) ||
		( targetBlock.name === 'omniform/hidden' && ! targetBlock.attributes?.fieldName )
	) {
		const defaultBlock = createBlock( getDefaultBlockName() );
		replaceBlock( targetBlock.clientId, defaultBlock );
		selectBlock( defaultBlock.clientId );
		return;
	}

	// Merge with the next or previous block normally.
	if ( forward ) {
		const nextBlockClientId = getNextBlockClientId( targetBlock.clientId );
		if ( nextBlockClientId ) {
			mergeBlocks( targetBlock.clientId, nextBlockClientId );
		}
	} else {
		const previousBlockClientId = getPreviousBlockClientId( targetBlock.clientId );
		if ( previousBlockClientId ) {
			mergeBlocks( previousBlockClientId, targetBlock.clientId );
		}
	}
}
