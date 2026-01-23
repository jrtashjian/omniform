/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies.
 */
import PostTypeDataView from '../post-type-data-view';

export default function FormList() {
	const fields = [
		{
			id: 'title',
			label: __( 'Form', 'omniform' ),
			render: ( { item } ) => item.title.rendered,
			enableHiding: false,
			filterBy: false,
		},
		{
			id: 'modified',
			label: __( 'Date', 'omniform' ),
			type: 'date',
			enableHiding: false,
			filterBy: false,
		},
	];

	const actions = [
		{
			id: 'edit',
			label: __( 'Edit', 'omniform' ),
			callback: ( items ) => console.debug( 'Edit action on items:', items[ 0 ] ),
			isPrimary: true,
		},
		{
			id: 'view',
			label: __( 'View', 'omniform' ),
			callback: ( items ) => console.debug( 'View action on items:', items[ 0 ] ),
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

	return (
		<PostTypeDataView
			pageTitle={ __( 'Forms', 'omniform' ) }
			fields={ fields }
			actions={ actions }
			postType="omniform"
			filterStatuses={ [ 'publish', 'draft', 'trash' ] }
			initialSortField="modified"
		/>
	);
}
