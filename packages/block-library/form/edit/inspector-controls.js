/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useEntityProp } from '@wordpress/core-data';
import { Button, PanelBody, TextControl } from '@wordpress/components';
import { InspectorControls } from '@wordpress/block-editor';
import { addQueryArgs } from '@wordpress/url';
import { commentContent } from '@wordpress/icons';

/**
 * Internal dependencies
 */
import { POST_TYPE, RESPONSE_POST_TYPE } from '../../shared/constants';

export default function FormInspectorControls( {
	formId,
	isEntityAvailable,
} ) {
	const [ title, setTitle ] = useEntityProp( 'postType', POST_TYPE, 'title', formId );

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
		</InspectorControls>
	);
}
