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
} from '@wordpress/block-editor';

const Edit = ( {
	attributes: { messageType, messageContent },
	setAttributes,
} ) => {
	const blockProps = useBlockProps( {
		className: classnames(
			`${ messageType }-response-notification`
		),
	} );

	return (
		<div { ...blockProps }>
			<RichText
				identifier="messageContent"
				aria-label={ __( 'Notification message content', 'omniform' ) }
				placeholder={ __( 'Enter your message here…', 'omniform' ) }
				value={ messageContent }
				onChange={ ( value ) => setAttributes( { messageContent: value } ) }
				preserveWhiteSpace
				withoutInteractiveFormatting
				allowedFormats={ [ 'core/bold', 'core/italic' ] }
			/>
			{ 'error' === messageType && (
				<p>{ __( 'Error Messages Placeholder', 'omniform' ) }</p>
			) }
		</div>
	);
};
export default Edit;
