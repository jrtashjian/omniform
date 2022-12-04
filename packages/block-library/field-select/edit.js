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
	InnerBlocks,
	RichText,
	useBlockProps,
	useInnerBlocksProps,
	store as blockEditorStore,
} from '@wordpress/block-editor';
import { createBlock, getDefaultBlockName } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import FormLabel from '../shared/form-label';

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
			[ 'omniform/select-option', { label: 'Option One' } ],
			[ 'omniform/select-option', { label: 'Option Two' } ],
			[ 'omniform/select-option', { label: 'Option Three' } ],
		],
		__experimentalCaptureToolbars: true,
		renderAppender: ( isSelected || hasSelectedInnerBlock ) && InnerBlocks.ButtonBlockAppender,
	} );

	return (
		<div
			{ ...blockProps }
			className={ classNames( blockProps.className, 'omniform-field-select', { [ `type-multiple` ]: isMultiple } ) }
		>
			<FormLabel originBlockProps={ props } />

			<div className="omniform-field-control">
				{ ! isMultiple && (
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
				) }
				{ ( isSelected || hasSelectedInnerBlock || isMultiple ) && (
					<ul { ...innerBlockProps } />
				) }
			</div>
		</div>
	);
};
export default Edit;
