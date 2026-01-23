/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { __experimentalHStack as HStack } from '@wordpress/components';

/**
 * Internal dependencies.
 */
import PostTypeDataView from '../post-type-data-view';

export default function ResponseList( {
	setActiveItem,
} ) {
	const fields = [
		{
			id: 'omniform_form.sender_email',
			label: __( 'Sender', 'omniform' ),
			render: ( { item } ) => (
				<HStack alignment="left">
					<div className="field__avatar">
						<img
							alt={ __( 'Author avatar' ) }
							src={ item.omniform_form.sender_gravatar }
						/>
					</div>
					<span className="field__email" style={ {
						fontWeight: [ 'omniform_unread', 'publish' ].includes( item.status ) ? 'bold' : 'normal',
					} }>
						{ item.omniform_form.sender_email }
					</span>
				</HStack>
			),
			enableHiding: false,
			enableSorting: false,
			filterBy: false,
		},
		{
			id: 'omniform_form.title',
			label: __( 'Form', 'omniform' ),
			enableHiding: false,
			enableSorting: false,
			filterBy: false,
		},
		{
			id: 'date',
			label: __( 'Date', 'omniform' ),
			type: 'date',
			enableHiding: false,
			filterBy: false,
		},
	];

	const actions = [
		{
			id: 'view',
			label: __( 'View', 'omniform' ),
			callback: ( items ) => setActiveItem( items[ 0 ] ),
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
			pageTitle={ __( 'Responses', 'omniform' ) }
			fields={ fields }
			actions={ actions }
			postType="omniform_response"
			statuses={ [ 'publish', 'omniform_unread', 'omniform_read' ] }
			filterStatuses={ [ 'omniform_unread', 'trash' ] }
			onClickItem={ ( item ) => setActiveItem( item ) }
		/>
	);
}
