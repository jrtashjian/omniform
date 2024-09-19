/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	BlockControls,
	RichText,
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';
import {
	ToolbarGroup,
	ToolbarButton,
} from '@wordpress/components';

/**
 * Internal dependencies
 */
import { iconReverseCondition } from '../shared/icons';

const Edit = ( {
	attributes: { callback, reverseCondition },
	setAttributes,
} ) => {
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
			<div { ...blockProps }>
				<div className="condition-label">
					{ conditionLabel }
					<RichText
						identifier="callback"
						placeholder={ __( 'Enter condition callback', 'omniform' ) }
						value={ callback || '' }
						onChange={ ( newCallback ) => {
							setAttributes( { callback: newCallback } );
						} }
						withoutInteractiveFormatting
						allowedFormats={ [] }
						disableLineBreaks
					/>
				</div>
				<div { ...innerBlockProps } />
			</div>
		</>
	);
};

export default Edit;
