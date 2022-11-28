/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import {
	InnerBlocks,
	RichText,
	useBlockProps,
	useInnerBlocksProps,
	store as blockEditorStore,
} from '@wordpress/block-editor';
import {
	Button,
	__experimentalHStack as HStack,
} from '@wordpress/components';
import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { chevronDown, chevronRight } from '@wordpress/icons';
import { createBlock } from '@wordpress/blocks';
import { useSelect } from '@wordpress/data';

const Edit = ( props ) => {
	const {
		attributes,
		setAttributes,
		mergeBlocks,
		onReplace,
		onRemove,
		clientId,
		isSelected,
	} = props;
	const {
		label,
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
		template: [
			[ 'omniform/select-option', { label: 'Option One' } ],
		],
		renderAppender: ( isSelected || hasSelectedInnerBlock ) && InnerBlocks.ButtonBlockAppender,
	} );

	const [ isOpened, setIsOpened ] = useState( ! label );

	return (
		<div
			{ ...blockProps }
			className={ classnames( blockProps.className, {
				[ `is-opened` ]: isOpened,
			} ) }
		>
			<HStack
				as="li"
				alignment="left"
			>
				<Button
					isSmall
					icon={ isOpened ? chevronDown : chevronRight }
					onClick={ () => setIsOpened( ! isOpened ) }
				/>
				<RichText
					identifier="label"
					aria-label={ __( 'Help text', 'omniform' ) }
					placeholder={ __( 'Write the option text…', 'omniform' ) }
					allowedFormats={ [] }
					withoutInteractiveFormatting
					value={ label }
					onChange={ ( html ) => setAttributes( { label: html } ) }
					onSplit={ ( value, isOriginal ) => {
						let newAttributes;

						if ( isOriginal || value ) {
							newAttributes = {
								...attributes,
								label: value.trim(),
							};
						}

						const block = createBlock( props.name, newAttributes );

						if ( isOriginal ) {
							block.clientId = clientId;
						}

						return block;
					} }
					onMerge={ mergeBlocks }
					onReplace={ onReplace }
					onRemove={ onRemove }
				/>
			</HStack>
			{ isOpened && <ul { ...innerBlockProps } /> }
		</div>
	);
};
export default Edit;
