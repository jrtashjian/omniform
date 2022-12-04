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
import { useEffect, useState } from '@wordpress/element';
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
		template: [
			[ 'omniform/select-option', { fieldLabel: 'Option One' } ],
		],
		renderAppender: ( isSelected || hasSelectedInnerBlock ) && InnerBlocks.ButtonBlockAppender,
	} );

	const [ isOpened, setIsOpened ] = useState( ! fieldLabel );
	useEffect( () => {
		if ( hasSelectedInnerBlock ) {
			setIsOpened( true );
		}
	}, [ hasSelectedInnerBlock ] );

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
					identifier="fieldLabel"
					aria-label={ __( 'Help text', 'omniform' ) }
					placeholder={ __( 'Write the option text…', 'omniform' ) }
					allowedFormats={ [] }
					withoutInteractiveFormatting
					value={ fieldLabel }
					onChange={ ( html ) => setAttributes( { fieldLabel: html } ) }
					onSplit={ ( value, isOriginal ) => {
						let newAttributes;

						if ( isOriginal || value ) {
							newAttributes = {
								...attributes,
								fieldLabel: value.trim(),
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
