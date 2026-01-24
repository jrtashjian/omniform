/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { addQueryArgs } from '@wordpress/url';
import { Button } from '@wordpress/components';
import {
	titleField,
	dateField,
} from '@wordpress/fields';

/**
 * Internal dependencies.
 */
import PostTypeDataView from '../post-type-data-view';

export default function FormList() {
	const fields = [
		titleField,
		dateField,
	];

	const actions = [
		{
			id: 'edit',
			label: __( 'Edit', 'omniform' ),
			callback: ( items ) => document.location.href = addQueryArgs( 'post.php', { post: items[ 0 ].id, action: 'edit' } ),
			isPrimary: true,
		},
		{
			id: 'view',
			label: __( 'View', 'omniform' ),
			callback: ( items ) => document.location.href = addQueryArgs( '/', { post_type: 'omniform', p: items[ 0 ].id } ),
			isPrimary: true,
		},
		{
			id: 'trash',
			label: __( 'Trash', 'omniform' ),
			callback: ( items ) => console.debug( 'Trash action on items:', items ),
			isPrimary: true,
			supportsBulk: true,
		},
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
