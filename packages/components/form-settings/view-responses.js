/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { PanelBody, Button } from '@wordpress/components';
import { addQueryArgs } from '@wordpress/url';
import { commentContent } from '@wordpress/icons';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';

/**
 * Internal dependencies
 */
import { RESPONSE_POST_TYPE } from '../../block-library/shared/constants';

export default function ViewResponses( {
	formId,
	isDocumentPanel,
} ) {
	const PanelComponent = isDocumentPanel
		? PluginDocumentSettingPanel
		: PanelBody;

	return (
		<PanelComponent
			name="omniform-view-responses"
			className="omniform-view-responses__panel"
		>
			<Button
				icon={ commentContent }
				href={ addQueryArgs( 'edit.php', {
					post_type: RESPONSE_POST_TYPE,
					omniform_id: formId,
				} ) }
			>
				{ __( 'View responses', 'omniform' ) }
			</Button>
		</PanelComponent>
	);
}
