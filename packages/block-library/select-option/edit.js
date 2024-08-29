/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	RichText,
	useBlockProps,
	store as blockEditorStore,
} from '@wordpress/block-editor';
import { useMergeRefs } from '@wordpress/compose';
import { createBlock } from '@wordpress/blocks';
import { useDispatch, useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
import useEnter from '../shared/hooks';

const Edit = ( props ) => {
	const {
		attributes,
		setAttributes,
		onRemove,
		onReplace,
		clientId,
	} = props;
	const {
		fieldLabel,
	} = attributes;

	const { insertBlocks } = useDispatch( blockEditorStore );
	const { getBlockRootClientId, getBlockIndex } = useSelect( blockEditorStore );

	const blockProps = useBlockProps( {
		ref: useMergeRefs( [ useEnter( clientId ) ] ),
		className: 'omniform-field-select-option',
	} );

	const onChangeFieldLabel = ( value ) => {
		// Handle the case where multiple lines are pasted.
		const lines = value.split( '<br>' ).filter( ( line ) => line.trim() );

		setAttributes( { fieldLabel: lines.shift() } );

		const blocks = lines.map( ( line ) =>
			createBlock( 'omniform/select-option', { fieldLabel: line } )
		);

		if ( blocks.length ) {
			insertBlocks(
				blocks,
				getBlockIndex( clientId ) + 1,
				getBlockRootClientId( clientId )
			);
		}
	};

	return (
		<div { ...blockProps }>
			<RichText
				ref={ useMergeRefs( [ useEnter( clientId ) ] ) }
				identifier="fieldLabel"
				aria-label={ __( 'Help text', 'omniform' ) }
				placeholder={ __( 'Write the option text…', 'omniform' ) }
				value={ fieldLabel }
				onChange={ onChangeFieldLabel }
				withoutInteractiveFormatting
				allowedFormats={ [] }
				disableLineBreaks
				onReplace={ onReplace }
				onRemove={ onRemove }
				__unstableAllowPrefixTransformations
			/>
		</div>
	);
};
export default Edit;
