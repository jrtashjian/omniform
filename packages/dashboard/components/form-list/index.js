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
			id: 'title.rendered',
			label: __( 'Form', 'omniform' ),
			enableHiding: false,
		},
		{
			id: 'date',
			label: __( 'Date', 'omniform' ),
			type: 'date',
			render: ( { item } ) => ( new Date( item.date ) ).toLocaleDateString(),
			enableHiding: false,
		},
	];

	return (
		<PostTypeDataView
			pageTitle={ __( 'Forms', 'omniform' ) }
			fields={ fields }
			postType="omniform"
			filterStatuses={ [ 'publish', 'draft', 'trash' ] }
		/>
	);
}
