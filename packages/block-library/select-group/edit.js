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
import { useSelect } from '@wordpress/data';
import { useMergeRefs } from '@wordpress/compose';

/**
 * Internal dependencies
 */
import useEnter from '../shared/hooks';

const Edit = ( props ) => {
	const {
		attributes,
		clientId,
		isSelected,
		onRemove,
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
		ref: useMergeRefs( [ useEnter( clientId ) ] ),
		className: 'omniform-select-group',
	} );

	const innerBlockProps = useInnerBlocksProps( {
		className: 'omniform-select-options-container',
	}, {
		allowedBlocks: [ 'omniform/select-option' ],
		template: [ [ 'omniform/select-option' ] ],
	} );

	return (
		<div { ...blockProps }>
			<HStack alignment="left">
				<Icon icon={ ( isSelected || hasSelectedInnerBlock ) ? chevronDown : chevronRight } />
				<RichText
					ref={ useMergeRefs( [ useEnter( clientId ) ] ) }
					identifier="fieldLabel"
					aria-label={ __( 'Help text', 'omniform' ) }
					placeholder={ __( 'Write the option text…', 'omniform' ) }
					value={ fieldLabel }
					onChange={ ( html ) => setAttributes( { fieldLabel: html } ) }
					withoutInteractiveFormatting
					allowedFormats={ [] }
					disableLineBreaks
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
