/**
 * WordPress dependencies
 */
import { __, sprintf } from '@wordpress/i18n';
import {
	useEntityProp,
	store as coreStore,
} from '@wordpress/core-data';
import { Button, PanelBody, TextControl, FormTokenField } from '@wordpress/components';
import { InspectorControls } from '@wordpress/block-editor';
import { addQueryArgs } from '@wordpress/url';
import { commentContent } from '@wordpress/icons';
import { useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
import { POST_TYPE, RESPONSE_POST_TYPE } from '../../shared/constants';

export default function FormInspectorControls( {
	formId,
	isEntityAvailable,
} ) {
	const {
		siteTitle,
		siteEmail,
	} = useSelect( ( select ) => {
		const { getEntityRecord } = select( coreStore );
		const settings = getEntityRecord( 'root', 'site' ) || {};
		return {
			siteTitle: settings?.title,
			siteEmail: settings?.email,
		};
	}, [] );

	const [ title, setTitle ] = useEntityProp( 'postType', POST_TYPE, 'title', formId );
	const [ meta, setMeta ] = useEntityProp( 'postType', POST_TYPE, 'meta', formId );

	const metaNotifyEmail = meta?.notify_email;
	const updateMetaNotifyEmail = ( newValue ) => {
		setMeta( { ...meta, notify_email: newValue } );
	};

	const metaNotifyEmailSubject = meta?.notify_email_subject;
	const updateMetaNotifyEmailSubject = ( newValue ) => {
		setMeta( { ...meta, notify_email_subject: newValue } );
	};

	return isEntityAvailable && (
		<InspectorControls>
			<PanelBody className="omniform-view-responses__panel">
				<Button
					icon={ commentContent }
					href={ addQueryArgs( 'edit.php', {
						post_type: RESPONSE_POST_TYPE,
						omniform_id: formId,
					} ) }
				>
					{ __( 'View responses', 'omniform' ) }
				</Button>
			</PanelBody>
			<PanelBody title={ __( 'Form Settings', 'omniform' ) }>
				<TextControl
					label={ __( 'Name', 'omniform' ) }
					value={ title }
					onChange={ setTitle }
					help={ __( 'This name will not be visible to viewers and is only for identifying the form.', 'omniform' ) }
				/>
				<Button
					variant="primary"
					href={ addQueryArgs( 'post.php', {
						action: 'edit',
						post: formId,
					} ) }
				>
					{ __( 'View in Form Editor', 'omniform' ) }
				</Button>
			</PanelBody>
			<PanelBody title={ __( 'Email Notification', 'omniform' ) }>
				<FormTokenField
					label={ __( 'Notify These Emails', 'omniform' ) }
					value={ metaNotifyEmail }
					onChange={ updateMetaNotifyEmail }
					placeholder={ siteEmail }
				/>
				<TextControl
					label={ __( 'Notification Subject', 'omniform' ) }
					value={ metaNotifyEmailSubject }
					onChange={ updateMetaNotifyEmailSubject }
					placeholder={ sprintf(
						// translators: %1$s represents the blog name, %2$s represents the form title.
						__( 'New Response: %1$s - %2$s', 'omniform' ),
						siteTitle, title
					) }
					help={ __( 'Write a short headline for the notification.', 'omniform' ) }
				/>
			</PanelBody>
		</InspectorControls>
	);
}
