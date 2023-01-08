/**
 * WordPress dependencies
 */
import {
	RichText,
	useBlockProps,
	useInnerBlocksProps,
	store as blockEditorStore,
} from '@wordpress/block-editor';
import {
	Icon,
	__experimentalHStack as HStack,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { chevronDown, chevronRight } from '@wordpress/icons';
import { cloneBlock, createBlock } from '@wordpress/blocks';
import { useDispatch, useSelect, useRegistry } from '@wordpress/data';

const Edit = ( props ) => {
	const {
		attributes,
		clientId,
		isSelected,
		mergeBlocks,
		onRemove,
		onReplace,
		setAttributes,
	} = props;
	const {
		fieldLabel,
	} = attributes;

	const hasSelectedInnerBlock = useSelect(
		( select ) => select( blockEditorStore ).hasSelectedInnerBlock( clientId ),
		[ clientId ]
	);

	const blockProps = useBlockProps( {
		className: 'omniform-select-group',
	} );

	const innerBlockProps = useInnerBlocksProps( {
		className: 'omniform-select-options-container',
	}, {
		allowedBlocks: [ 'omniform/select-option' ],
		template: [ [ 'omniform/select-option' ] ],
	} );

	const registry = useRegistry();

	const {
		getBlock,
		getBlockIndex,
		getBlockListSettings,
		getBlockOrder,
		getBlockRootClientId,
	} = useSelect( blockEditorStore );

	const {
		moveBlocksToPosition,
		replaceBlock,
		updateBlockListSettings,
	} = useDispatch( blockEditorStore );

	const onMerge = ( forward ) => {
		if ( forward ) {
			return mergeBlocks( forward );
		}

		const parentId = getBlockRootClientId( clientId );
		const nextOptionIndex = getBlockIndex( clientId ) + 1;

		registry.batch( () => {
			// Move existing omniform/select-option blocks out of group.
			moveBlocksToPosition(
				getBlockOrder( clientId ),
				clientId,
				parentId,
				nextOptionIndex
			);

			// Convert select-group to select-option.
			replaceBlock( clientId, createBlock( 'omniform/select-option', attributes ) );
			updateBlockListSettings( clientId, getBlockListSettings( parentId ) );
		}, [] );
	};

	return (
		<div { ...blockProps }>
			<HStack alignment="left">
				<Icon icon={ ( isSelected || hasSelectedInnerBlock ) ? chevronDown : chevronRight } />
				<RichText
					identifier="fieldLabel"
					aria-label={ __( 'Help text', 'omniform' ) }
					placeholder={ __( 'Write the option text…', 'omniform' ) }
					value={ fieldLabel }
					onChange={ ( html ) => setAttributes( { fieldLabel: html } ) }
					withoutInteractiveFormatting
					allowedFormats={ [] }
					onSplit={ ( value, isOriginal ) => {
						let block;

						if ( isOriginal || value ) {
							block = cloneBlock( getBlock( clientId ), {
								fieldLabel: value.trim(),
							} );
						} else {
							block = createBlock( props.name, {
								fieldLabel: value.trim(),
							} );
						}

						if ( isOriginal ) {
							block.clientId = clientId;
						}

						return block;
					} }
					onMerge={ onMerge }
					onReplace={ onReplace }
					onRemove={ onRemove }
				/>
			</HStack>
			{ ( isSelected || hasSelectedInnerBlock ) && (
				<div { ...innerBlockProps } />
			) }
		</div>
	);
};
export default Edit;
