/**
 * WordPress dependencies
 */
import { __, sprintf } from '@wordpress/i18n';
import { store as coreStore } from '@wordpress/core-data';
import { useSelect } from '@wordpress/data';
import { PanelBody, TextControl, FormTokenField } from '@wordpress/components';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';

export default function EmailNotificationSettings( {
	isDocumentPanel,
	getSetting,
	setSetting,
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

	// Hide email notification settings if the form type is custom.
	if ( 'custom' === getSetting( 'form_type' ) ) {
		return null;
	}

	const placeholder = getSetting( 'form_title' )
		? sprintf(
			// translators: %1$s represents the blog name, %2$s represents the form title.
			__( 'New Response: %1$s - %2$s', 'omniform' ),
			siteTitle, getSetting( 'form_title' )
		)
		: sprintf(
			// translators: %1$s represents the blog name
			__( 'New Response: %1$s', 'omniform' ),
			siteTitle
		);

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
				value={ getSetting( 'notify_email' ) }
				onChange={ ( newValue ) => setSetting( 'notify_email', newValue ) }
				placeholder={ siteEmail }
			/>
			<TextControl
				label={ __( 'Notification Subject', 'omniform' ) }
				value={ getSetting( 'notify_email_subject' ) }
				onChange={ ( newValue ) => setSetting( 'notify_email_subject', newValue ) }
				placeholder={ placeholder }
				help={ __( 'Write a short headline for the notification.', 'omniform' ) }
				__nextHasNoMarginBottom
				__next40pxDefaultSize
			/>
		</PanelComponent>
	);
}
