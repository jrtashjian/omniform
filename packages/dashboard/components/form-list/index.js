/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { addQueryArgs } from '@wordpress/url';
import {
	Button,
} from '@wordpress/components';
import {
	titleField,
	statusField,
	dateField,
	viewPost,
	duplicatePost,
} from '@wordpress/fields';

/**
 * Internal dependencies.
 */
import PostTypeDataView from '../post-type-data-view';
import editPost from '../../actions/edit-post';

export default function FormList() {
	const fields = [
		titleField,
		statusField,
		dateField,
	];

	const actions = [
		editPost,
		viewPost,
		duplicatePost,
	];

	const pageActions = (
		<>
			<Button
				variant="primary"
				onClick={ () => document.location.href = addQueryArgs( 'post-new.php', { post_type: 'omniform' } ) }
			>
				{ __( 'Create Form', 'omniform' ) }
			</Button>
		</>
	);

	return (
		<PostTypeDataView
			pageTitle={ __( 'Forms', 'omniform' ) }
			pageActions={ pageActions }
			fields={ fields }
			actions={ actions }
			postType="omniform"
			filterStatuses={ [ 'publish', 'draft', 'trash' ] }
			onClickItem={ ( item ) => document.location.href = addQueryArgs( 'post.php', { post: item.id, action: 'edit' } ) }
		/>
	);
}
