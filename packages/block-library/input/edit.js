/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	RichText,
	store as blockEditorStore,
	useBlockProps,
} from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';

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
	attributes: { fieldPlaceholder, fieldType },
	setAttributes,
	clientId,
	isSelected,
} ) => {
	const {
		getBlock,
		getBlockRootClientId,
	} = useSelect( blockEditorStore );

	const isTextInput = [ 'text', 'email', 'url', 'number', 'month', 'password', 'search', 'tel', 'week', 'hidden' ].includes( fieldType );
	const isOptionInput = [ 'checkbox', 'radio' ].includes( fieldType );

	const blockProps = useBlockProps();

	if ( isTextInput ) {
		return (
			<RichText
				{ ...blockProps }
				identifier="fieldControl"
				aria-label={ __( 'Placeholder text for text input.', 'omniform' ) }
				placeholder={
					( isSelected || fieldPlaceholder ) ? __( 'Enter a placeholder…', 'omniform' ) : undefined
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
		);
	}

	if ( isOptionInput ) {
		return ( <div type={ fieldType } { ...blockProps } /> );
	}

	return (
		<input type={ fieldType } { ...blockProps } readOnly />
	);
};

export default Edit;
