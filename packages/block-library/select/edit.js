/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';
import {
	RichText,
	useBlockProps,
	useInnerBlocksProps,
	store as blockEditorStore,
} from '@wordpress/block-editor';

/**
 * Internal dependencies
 */
import {
	onMerge,
	onRemove,
	onReplace,
	onSplit,
} from '../shared/rich-text-handlers';

const Edit = ( {
	attributes: { fieldPlaceholder, isMultiple },
	setAttributes,
	isSelected,
	clientId,
} ) => {
	const {
		getBlock,
		getBlockRootClientId,
	} = useSelect( blockEditorStore );

	const hasSelectedInnerBlock = useSelect(
		( select ) => select( blockEditorStore ).hasSelectedInnerBlock( clientId, true ),
		[ clientId ]
	);

	// Indent the option listing select-group blocks are found.
	const hasNestedOptions = useSelect( ( select ) => {
		const blocksSelectGroup = select( blockEditorStore ).getBlocks( clientId )
			.filter( ( block ) => block.name === 'omniform/select-group' );
		return !! blocksSelectGroup.length;
	} );

	const blockProps = useBlockProps( {
		className: classNames( {
			[ `type-multiple` ]: isMultiple,
			[ `options-nested` ]: hasNestedOptions,
		} ),
	} );

	const innerBlockProps = useInnerBlocksProps( {
		className: 'omniform-select-options-container',
	}, {
		templateLock: false,
		allowedBlocks: [ 'omniform/select-option', 'omniform/select-group' ],
		template: [ [ 'omniform/select-option' ] ],
	} );

	if ( isMultiple ) {
		// Set height to min height if block is not selected. This is the default behavior of the minHeight on <select> elements.
		// If the block is selected, it should be allowed to expand to show all options.
		const height = ( isSelected || hasSelectedInnerBlock ) ? undefined : ( blockProps.style.minHeight || '230px' );

		return (
			<div
				{ ...blockProps }
				style={ {
					...blockProps.style,
					height,
				} }
			>
				<div { ...innerBlockProps } />
			</div>
		);
	}

	return (
		<div
			{ ...blockProps }
			style={ {
				...blockProps.style,
				minHeight: undefined,
			} }
		>
			{ ! isMultiple && (
				<RichText
					identifier="fieldControl"
					aria-label={ __( 'Placeholder text for text input.', 'omniform' ) }
					placeholder={
						( ( fieldPlaceholder || ! isSelected ) && ! hasSelectedInnerBlock )
							? undefined
							: __( 'Enter a placeholder…', 'omniform' )
					}
					value={ fieldPlaceholder }
					onChange={ ( html ) => setAttributes( { fieldPlaceholder: html } ) }
					withoutInteractiveFormatting
					allowedFormats={ [] }

					onSplit={ ( ...args ) => onSplit( getBlock( getBlockRootClientId( clientId ) ), ...args ) }
					onReplace={ ( ...args ) => onReplace( getBlock( getBlockRootClientId( clientId ) ), ...args ) }
					onMerge={ ( ...args ) => onMerge( getBlock( getBlockRootClientId( clientId ) ), ...args ) }
					onRemove={ ( ...args ) => onRemove( getBlock( getBlockRootClientId( clientId ) ), ...args ) }
				/>
			) }

			{ ( isSelected || hasSelectedInnerBlock || isMultiple ) && (
				<div { ...innerBlockProps } />
			) }
		</div>
	);
};

export default Edit;
