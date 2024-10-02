/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';
import { store as blocksStore } from '@wordpress/blocks';
import {
	BlockControls,
	useBlockProps,
	useInnerBlocksProps,
	store as blockEditorStore,
	InspectorControls,
} from '@wordpress/block-editor';
import {
	ToolbarGroup,
	ToolbarButton,
	PanelBody,
	TextControl,
	ToggleControl,
} from '@wordpress/components';

/**
 * Internal dependencies
 */
import { iconReverseCondition } from '../shared/icons';

const Edit = ( {
	attributes: { callback, reverseCondition },
	setAttributes,
	clientId,
} ) => {
	const blockTitle = useSelect(
		( select ) => {
			if ( ! clientId ) {
				return null;
			}

			const {
				getBlockName,
				getBlockAttributes,
			} = select( blockEditorStore );

			const {
				getBlockType,
				getActiveBlockVariation,
			} = select( blocksStore );

			const blockName = getBlockName( clientId );
			const blockType = getBlockType( blockName );
			if ( ! blockType ) {
				return null;
			}

			const attributes = getBlockAttributes( clientId );
			const match = getActiveBlockVariation( blockName, attributes );
			return match?.title || attributes?.callback;
		},
		[ clientId ]
	);

	const blockProps = useBlockProps();
	const innerBlockProps = useInnerBlocksProps();

	const conditionLabel = reverseCondition
		? __( 'Show when not', 'omniform' )
		: __( 'Show when', 'omniform' );
	return (
		<>
			<BlockControls>
				<ToolbarGroup>
					<ToolbarButton
						icon={ iconReverseCondition }
						isActive={ reverseCondition }
						label={ __( 'Reverse condition', 'omniform' ) }
						onClick={ () => setAttributes( { reverseCondition: ! reverseCondition } ) }
					/>
				</ToolbarGroup>
			</BlockControls>
			<InspectorControls>
				<PanelBody title={ __( 'Conditional Settings', 'omniform' ) }>
					<TextControl
						label={ __( 'Callback', 'omniform' ) }
						value={ callback || '' }
						onChange={ ( newValue ) => setAttributes( { callback: newValue } ) }
						help={ __( 'The callback to determine if this block should be shown.', 'omniform' ) }
					/>

					<ToggleControl
						label={ __( 'Reverse condition', 'omniform' ) }
						checked={ !! reverseCondition }
						onChange={ () => setAttributes( { reverseCondition: ! reverseCondition } ) }
						help={ __( 'Show the block when the callback returns false.', 'omniform' ) }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				<div className="condition-label">
					{ conditionLabel + ' ' + blockTitle.toLowerCase() }
				</div>
				<div { ...innerBlockProps } />
			</div>
		</>
	);
};

export default Edit;
