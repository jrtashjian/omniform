/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { __experimentalHStack as HStack } from '@wordpress/components';

/**
 * Internal dependencies.
 */
import PostTypeDataView from '../post-type-data-view';

export default function ResponseList() {
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
		},
		{
			id: 'omniform_form.title',
			label: __( 'Form', 'omniform' ),
			enableHiding: false,
			enableSorting: false,
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
			pageTitle={ __( 'Responses', 'omniform' ) }
			fields={ fields }
			postType="omniform_response"
			statuses={ [ 'publish', 'omniform_unread', 'omniform_read' ] }
			filterStatuses={ [ 'omniform_unread', 'trash' ] }
		/>
	);
}
