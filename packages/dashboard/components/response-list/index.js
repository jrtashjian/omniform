/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { Button } from '@wordpress/components';
import { dateField } from '@wordpress/fields';

/**
 * Internal dependencies.
 */
import PostTypeDataView from '../post-type-data-view';

export default function ResponseList( {
	setActiveItem,
} ) {
	const STATUSES = [
		{ value: 'trash', label: __( 'Trash', 'omniform' ) },
		{ value: 'omniform_read', label: __( 'Read', 'omniform' ) },
		{ value: 'omniform_unread', label: __( 'Unread', 'omniform' ) },
	];

	const fields = [
		{
			id: 'omniform_form.sender_email',
			label: __( 'Sender', 'omniform' ),
			render: ( { item } ) => (
				<span style={ {
					fontWeight: item.status === 'omniform_unread' ? 'bold' : 'normal',
				} }>
					{ item.omniform_form.sender_email }
				</span>
			),
			enableHiding: false,
			enableSorting: false,
			filterBy: false,
		},
		{
			id: 'omniform_form.sender_ip',
			label: __( 'Sender IP', 'omniform' ),
		},
		{
			id: 'omniform_form.sender_gravatar',
			label: __( 'Avatar', 'omniform' ),
			isVisible: () => false,
			render: ( { item } ) => (
				<img alt={ __( 'Sender avatar', 'omniform' ) } src={ item.omniform_form.sender_gravatar } style={ { width: '40px', height: '40px' } } />
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
			id: 'status',
			label: __( 'Status', 'omniform' ),
			elements: STATUSES,
			filterBy: {
				operators: [ 'isAny' ],
			},
			enableSorting: false,
		},
		dateField,
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

	const pageActions = (
		<>
			<Button
				variant="primary"
				onClick={ () => {} }
			>
				{ __( 'Export', 'omniform' ) }
			</Button>
		</>
	);

	return (
		<PostTypeDataView
			pageTitle={ __( 'Responses', 'omniform' ) }
			pageSubTitle={ __( 'Manage your form responses.', 'omniform' ) }
			pageActions={ pageActions }
			fields={ fields }
			actions={ actions }
			postType="omniform_response"
			statuses={ [ 'omniform_unread', 'omniform_read' ] }
			filterStatuses={ [ 'omniform_unread', 'trash' ] }
			mediaField="omniform_form.sender_gravatar"
			descriptionField="omniform_form.sender_ip"
			onClickItem={ ( item ) => setActiveItem( item ) }
		/>
	);
}
