/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	RichText,
	useBlockProps,
	__experimentalGetElementClassName,
} from '@wordpress/block-editor';
import { useMergeRefs } from '@wordpress/compose';

/**
 * Internal dependencies
 */
import useEnter from '../shared/hooks';

const Edit = ( {
	attributes,
	setAttributes,
	onRemove,
	clientId,
} ) => {
	const {
		buttonLabel,
	} = attributes;

	const blockProps = useBlockProps( {
		ref: useMergeRefs( [ useEnter( clientId ) ] ),
		className: classnames(
			__experimentalGetElementClassName( 'button' ),
			'wp-block-button__link',
		),
	} );

	return (
		<div>
			<RichText
				{ ...blockProps }
				identifier="buttonLabel"
				aria-label={ __( 'Button text', 'omniform' ) }
				placeholder={ __( 'Add text…', 'omniform' ) }
				value={ buttonLabel }
				onChange={ ( value ) => setAttributes( { buttonLabel: value } ) }
				withoutInteractiveFormatting
				allowedFormats={ [ 'core/bold', 'core/italic', 'core/image' ] }
				disableLineBreaks
				onRemove={ onRemove }
			/>
		</div>
	);
};
export default Edit;
