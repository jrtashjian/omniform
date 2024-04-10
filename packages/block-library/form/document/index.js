/**
 * WordPress dependencies
 */
import { useSelect } from '@wordpress/data';
import {
	store as editorStore,
} from '@wordpress/editor';
import { registerPlugin } from '@wordpress/plugins';

/**
 * Internal dependencies
 */
import EmailNotificationSettings from '../../../components/form-settings/email-notifications';
import ViewResponses from '../../../components/form-settings/view-responses';
import { POST_TYPE } from '../../shared/constants';

const DocumentSettingsPanel = () => {
	const {
		postType,
		postId,
	} = useSelect( ( select ) => {
		return {
			postType: select( editorStore ).getCurrentPostType(),
			postId: select( editorStore ).getCurrentPostId(),
		};
	} );

	return POST_TYPE === postType && (
		<>
			<ViewResponses formId={ postId } isDocumentPanel />
			<EmailNotificationSettings formId={ postId } isDocumentPanel />
		</>
	);
};

registerPlugin( 'omniform', {
	render: DocumentSettingsPanel,
} );
