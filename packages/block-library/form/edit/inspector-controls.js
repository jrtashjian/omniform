/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	useEntityProp,
} from '@wordpress/core-data';
import { Button, PanelBody, TextControl } from '@wordpress/components';
import { InspectorControls } from '@wordpress/block-editor';
import { addQueryArgs } from '@wordpress/url';

/**
 * Internal dependencies
 */
import { POST_TYPE } from '../../shared/constants';
import EmailNotificationSettings from '../../../components/form-settings/email-notifications';
import ViewResponses from '../../../components/form-settings/view-responses';
import SubmissionMethodSettings from '../../../components/form-settings/submission-method';
import { useStandaloneFormSettings, useStandardFormSettings } from '../../../block-library/form/utils/hooks';

export default function FormInspectorControls( {
	formId,
	blockObject,
} ) {
	return !! formId
		? <StandardFormInspectorControls formId={ formId } />
		: <StandaloneFormInspectorControls blockObject={ blockObject } />;
}

function StandardFormInspectorControls( { formId } ) {
	const [ title, setTitle ] = useEntityProp( 'postType', POST_TYPE, 'title', formId );

	const {
		getSetting,
		setSetting,
	} = useStandardFormSettings( formId );

	return (
		<InspectorControls>
			<ViewResponses formId={ formId } />
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
			<EmailNotificationSettings
				getSetting={ getSetting }
				setSetting={ setSetting }
			/>
			<SubmissionMethodSettings
				getSetting={ getSetting }
				setSetting={ setSetting }
			/>
		</InspectorControls>
	);
}

function StandaloneFormInspectorControls( { blockObject } ) {
	const {
		getSetting,
		setSetting,
	} = useStandaloneFormSettings( blockObject );

	return (
		<InspectorControls>
			<EmailNotificationSettings
				getSetting={ getSetting }
				setSetting={ setSetting }
			/>
			<SubmissionMethodSettings
				getSetting={ getSetting }
				setSetting={ setSetting }
			/>
		</InspectorControls>
	);
}
