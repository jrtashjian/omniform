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
	BlockControls,
	InnerBlocks,
	RichText,
	useBlockProps,
	useInnerBlocksProps,
	store as blockEditorStore,
} from '@wordpress/block-editor';
import { createBlock, getDefaultBlockName } from '@wordpress/blocks';
import {
	ToolbarButton,
	ToolbarGroup,
	ResizableBox,
} from '@wordpress/components';

/**
 * Internal dependencies
 */
import FormLabel from '../shared/form-label';
import { Required } from '../shared/icons';

const Edit = ( props ) => {
	const {
		attributes,
		setAttributes,
		isSelected,
		clientId,
	} = props;
	const {
		fieldPlaceholder,
		isMultiple,
		isRequired,
		height,
	} = attributes;

	const hasSelectedInnerBlock = useSelect(
		( select ) => select( blockEditorStore ).hasSelectedInnerBlock( clientId, true ),
		[ clientId ]
	);

	const blockProps = useBlockProps();

	const innerBlockProps = useInnerBlocksProps( {
		className: 'omniform-select-options-container',
	}, {
		allowedBlocks: [ 'omniform/select-option', 'omniform/select-group' ],
		template: [
			[ 'omniform/select-option', { fieldLabel: 'Option One' } ],
			[ 'omniform/select-option', { fieldLabel: 'Option Two' } ],
			[ 'omniform/select-option', { fieldLabel: 'Option Three' } ],
		],
		__experimentalCaptureToolbars: true,
		renderAppender: ( isSelected || hasSelectedInnerBlock ) && InnerBlocks.ButtonBlockAppender,
	} );

	return (
		<div
			{ ...blockProps }
			className={ classNames( blockProps.className, 'omniform-field-select', {
				[ `type-multiple` ]: isMultiple,
				[ `field-required` ]: isRequired,
			} ) }
		>
			<FormLabel originBlockProps={ props } />

			{ isMultiple && (
				<ResizableBox
					className="omniform-field-control"
					size={ { height: height || 'auto' } }
					showHandle={ isSelected }
					enable={ { bottom: true } }
					onResizeStop={ ( _event, _direction, elt ) => {
						setAttributes( { height: parseInt( elt.style.height ) } );
					} }
				>
					<div { ...innerBlockProps } />
				</ResizableBox>
			) }

			{ ! isMultiple && (
				<div className="omniform-field-control">
					<RichText
						identifier="fieldControl"
						className="placeholder-text"
						aria-label={ __( 'Help text', 'omniform' ) }
						placeholder={
							( fieldPlaceholder || ( ! isSelected && ! hasSelectedInnerBlock ) )
								? undefined
								: __( 'Enter a placeholder…', 'omniform' )
						}
						allowedFormats={ [] }
						withoutInteractiveFormatting
						value={ fieldPlaceholder }
						onChange={ ( html ) => setAttributes( { fieldPlaceholder: html } ) }
						// When hitting enter, place a new insertion point. This makes adding field a lot easier.
						onSplit={ ( _value, isOriginal ) => {
							const block = isOriginal
								? createBlock( props.name, props.attributes )
								: createBlock( getDefaultBlockName() );

							if ( isOriginal ) {
								block.clientId = props.clientId;
							}

							return block;
						} }
						onReplace={ props.onReplace }
					/>
					{ ( isSelected || hasSelectedInnerBlock ) && (
						<div { ...innerBlockProps } />
					) }
				</div>
			) }

			<BlockControls>
				<ToolbarGroup>
					<ToolbarButton
						icon={ Required }
						isActive={ isRequired }
						label={ __( 'Required field', 'omniform' ) }
						onClick={ () => setAttributes( { isRequired: ! isRequired } ) }
					/>
				</ToolbarGroup>
			</BlockControls>
		</div>
	);
};
export default Edit;
