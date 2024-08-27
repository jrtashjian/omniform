/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';
import { useMergeRefs } from '@wordpress/compose';

/**
 * Internal dependencies
 */
import useEnter from '../shared/hooks';

const Edit = ( props ) => {
	const {
		attributes,
		setAttributes,
		onRemove,
		clientId,
	} = props;
	const {
		fieldLabel,
	} = attributes;

	const blockProps = useBlockProps( {
		ref: useMergeRefs( [ useEnter( clientId ) ] ),
		className: 'omniform-field-select-option',
	} );

	return (
		<div { ...blockProps }>
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
				__unstableAllowPrefixTransformations
			/>
		</div>
	);
};
export default Edit;
