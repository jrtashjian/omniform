/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	RichText,
	store as blockEditorStore,
	useBlockProps,
} from '@wordpress/block-editor';
import { useEntityProp } from '@wordpress/core-data';
import { cloneBlock, createBlock } from '@wordpress/blocks';
import { useSelect, useDispatch } from '@wordpress/data';
import { cleanForSlug } from '@wordpress/url';

const Edit = ( {
	attributes: { fieldLabel, isHidden },
	setAttributes,
	clientId,
	context,
} ) => {
	const {
		getBlock,
		getBlockRootClientId,
		getPreviousBlockClientId,
		getNextBlockClientId,
	} = useSelect( blockEditorStore );

	const {
		mergeBlocks,
		replaceBlocks,
		selectBlock,
		removeBlock,
		updateBlockAttributes,
	} = useDispatch( blockEditorStore );

	const contextFieldName = context[ 'omniform/fieldName' ] || '';
	const contextFieldLabel = context[ 'omniform/fieldLabel' ] || '';

	/**
	 * Updates the field label of the parent block.
	 *
	 * @param {string} value The new field label.
	 */
	const updateLabel = ( value ) => {
		const cleanLabel = cleanForSlug( contextFieldLabel.replace( /(<([^>]+)>)/gi, '' ) );

		if ( ! contextFieldName || contextFieldName === cleanLabel ) {
			updateBlockAttributes(
				getBlockRootClientId( clientId ),
				{
					fieldLabel: value,
					fieldName: cleanForSlug( value.replace( /(<([^>]+)>)/gi, '' ) ),
				}
			);
		} else {
			updateBlockAttributes(
				getBlockRootClientId( clientId ),
				{ fieldLabel: value }
			);
		}
	};

	// Manage the required label.
	const { postId: contextPostId, postType: contextPostType } = context;

	const [ meta, setMeta ] = useEntityProp( 'postType', contextPostType, 'meta', contextPostId );

	const metaRequiredLabel = meta?.required_label;
	const updateMetaRequiredLabel = ( newValue ) => {
		setMeta( { ...meta, required_label: newValue } );
	};

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

		if ( isOriginal || value ) {
			block = cloneBlock(
				rootBlock,
				{
					fieldLabel: value,
					fieldName: cleanForSlug( value.replace( /(<([^>]+)>)/gi, '' ) ),
				},
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
	 * Handles removing the parent field block.
	 */
	const onRemove = () => {
		removeBlock( getBlockRootClientId( clientId ) );
	};

	/**
	 * Handles merging the parent field block with the next or previous block.
	 *
	 * @param {boolean} forward Whether to merge with the next block or the previous block.
	 */
	const onMerge = ( forward ) => {
		const rootClientId = getBlockRootClientId( clientId );

		if ( forward ) {
			const nextBlockClientId = getNextBlockClientId( rootClientId );

			// If there is a next block, merge with it.
			if ( nextBlockClientId ) {
				mergeBlocks( rootClientId, nextBlockClientId );
				selectLabelBlock( rootClientId );
			}
		} else {
			const previousBlockClientId = getPreviousBlockClientId( rootClientId );

			// If there is a previous block, merge with it otherwise remove the field.
			if ( previousBlockClientId ) {
				// If the previous block is a field, merge with it or replace it with a paragraph.
				if ( !! contextFieldLabel ) {
					mergeBlocks( previousBlockClientId, rootClientId );
					selectLabelBlock( previousBlockClientId, -1 );
				} else {
					replaceBlocks(
						[ previousBlockClientId ],
						[
							cloneBlock( getBlock( previousBlockClientId ) ),
							createBlock( 'core/paragraph' ),
						],
						1
					);
				}
			}
		}
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

	return (
		<div { ...blockProps }>
			<RichText
				identifier="fieldLabel"
				placeholder={ __( 'Enter a label for the field…', 'omniform' ) }
				value={ fieldLabel || contextFieldLabel }
				onChange={ updateLabel }
				withoutInteractiveFormatting
				allowedFormats={ [ 'core/bold', 'core/italic', 'core/image' ] }
				onSplit={ onSplit }
				onReplace={ onReplace }
				onRemove={ onRemove }
				onMerge={ onMerge }
			/>

			{ context[ 'omniform/isRequired' ] && (
				<RichText
					identifier="requiredLabel"
					className="omniform-field-required"
					placeholder={ __( 'Enter a required field label…', 'omniform' ) }
					value={ metaRequiredLabel }
					onChange={ updateMetaRequiredLabel }
					withoutInteractiveFormatting
					allowedFormats={ [ 'core/bold', 'core/italic', 'core/image' ] }
					multiline={ false }
					onSplit={ false }
					onReplace={ false }
				/>
			) }
		</div>
	);
};

export default Edit;
