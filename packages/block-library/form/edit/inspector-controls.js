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

export default function FormInspectorControls( {
	formId,
	isEntityAvailable,
} ) {
	const [ title, setTitle ] = useEntityProp( 'postType', POST_TYPE, 'title', formId );

	return isEntityAvailable && (
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
			<EmailNotificationSettings formId={ formId } />
		</InspectorControls>
	);
}
