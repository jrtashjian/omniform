/**
 * WordPress dependencies
 */
import { __, sprintf } from '@wordpress/i18n';
import {
	useEntityProp,
	store as coreStore,
} from '@wordpress/core-data';
import { useSelect } from '@wordpress/data';
import { PanelBody, TextControl, FormTokenField } from '@wordpress/components';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';

/**
 * Internal dependencies
 */
import { POST_TYPE } from '../../block-library/shared/constants';

export default function EmailNotificationSettings( {
	formId,
	isDocumentPanel,
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

	const [ title ] = useEntityProp( 'postType', POST_TYPE, 'title', formId );
	const [ meta, setMeta ] = useEntityProp( 'postType', POST_TYPE, 'meta', formId );

	const metaNotifyEmail = meta?.notify_email;
	const updateMetaNotifyEmail = ( newValue ) => {
		setMeta( { ...meta, notify_email: newValue } );
	};

	const metaNotifyEmailSubject = meta?.notify_email_subject;
	const updateMetaNotifyEmailSubject = ( newValue ) => {
		setMeta( { ...meta, notify_email_subject: newValue } );
	};

	const PanelComponent = isDocumentPanel
		? PluginDocumentSettingPanel
		: PanelBody;

	return (
		<PanelComponent
			name="omniform-email-notification"
			title={ __( 'Email Notification', 'omniform' ) }
		>
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
		</PanelComponent>
	);
}
