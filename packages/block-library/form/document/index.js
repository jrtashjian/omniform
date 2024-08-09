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
import { POST_TYPE } from '../../shared/constants';
import SubmissionMethodSettings from '../../../components/form-settings/submission-method';
import { useStandardFormSettings } from '../utils/hooks';

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
		<FormSettingsPanel postId={ postId } />
	);
};

function FormSettingsPanel( { postId } ) {
	const {
		getSetting,
		setSetting,
	} = useStandardFormSettings( postId );

	return (
		<>
			<EmailNotificationSettings
				getSetting={ getSetting }
				setSetting={ setSetting }
				isDocumentPanel
			/>
			<SubmissionMethodSettings
				getSetting={ getSetting }
				setSetting={ setSetting }
				isDocumentPanel
			/>
		</>
	);
}

registerPlugin( 'omniform', {
	render: DocumentSettingsPanel,
} );
