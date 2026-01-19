/**
 * WordPress dependencies.
 */
import { useState, useMemo } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import {
	Popover,
	SlotFillProvider,
	__experimentalHStack as HStack,
	Button,
} from '@wordpress/components';
import { Page } from '@wordpress/admin-ui';
import { DataViews } from '@wordpress/dataviews/wp';
import {
	useEntityRecords,
	store as coreStore,
} from '@wordpress/core-data';
import { useDispatch } from '@wordpress/data';
import { store as noticesStore } from '@wordpress/notices';
import { EditorSnackbars } from '@wordpress/editor';

/**
 * Internal dependencies.
 */
import { iconItemMarkRead, iconItemMarkUnread, iconItemView, iconItemTrash } from '../../../block-library/shared/icons';

export default function App( { settings } ) {
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
					<span className="field__email">
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
		},
		{
			id: 'status',
			label: __( 'Status', 'omniform' ),
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

	const titleField = 'omniform_form.sender_email';

	const [ view, setView ] = useState( {
		type: 'table',
		search: '',
		page: 1,
		perPage: 10,
		sort: {
			field: 'date',
			direction: 'desc',
		},
		titleField,
		fields: fields.map( ( field ) => field.id ).filter( ( id ) => id !== titleField ),
		layout: {
			enableMoving: false,
		},
	} );

	const queryArgs = useMemo( () => {
		return {
			per_page: view.perPage,
			page: view.page,
			order: view.sort?.direction,
			orderby: view.sort?.field,
			search: view.search,
			status: [ 'publish', 'omniform_unread', 'omniform_read' ],
		};
	}, [ view ] );

	const {
		records = [],
		isResolving: isLoadingData,
		totalItems,
		totalPages,
	} = useEntityRecords( 'postType', 'omniform_response', queryArgs );

	// Change Status Action
	const { editEntityRecord, saveEditedEntityRecord } = useDispatch( coreStore );
	const { createSuccessNotice, createErrorNotice } = useDispatch( noticesStore );

	async function onChangeStatus( items, status ) {
		console.debug( 'change-status', items, status );

		try {
			for ( const item of items ) {
				await editEntityRecord( 'postType', item.type, item.id, { status } );
				await saveEditedEntityRecord( 'postType', item.type, item.id, { throwOnError: true } );
			}
			createSuccessNotice( __( 'Status updated successfully.', 'omniform' ), { type: 'snackbar' } );
		} catch ( error ) {
			createErrorNotice( __( 'Error updating status.', 'omniform' ), { type: 'snackbar' } );
		}
	}

	return (
		<SlotFillProvider>
			<EditorSnackbars />
			<Page
				title={ __( 'OmniForm', 'omniform' ) }
				actions={
					<>
						<Button variant="primary">
							{ __( 'Primary Action', 'omniform' ) }
						</Button>
					</>
				}
			>
				<DataViews
					data={ records || [] }
					isLoading={ isLoadingData }
					view={ view }
					onChangeView={ setView }
					fields={ fields }
					paginationInfo={ { totalItems, totalPages } }
					defaultLayouts={ { table: {} } }
					actions={ [
						{
							id: 'view',
							icon: iconItemView,
							label: __( 'View', 'omniform' ),
							isPrimary: true,
							callback: ( items ) => console.debug( 'view', items ),
						},
						{
							id: 'mark-read',
							icon: iconItemMarkRead,
							label: __( 'Mark Read', 'omniform' ),
							isPrimary: true,
							isEligible: ( item ) => item.status !== 'omniform_read',
							callback: ( items ) => onChangeStatus( items, 'omniform_read' ),
						},
						{
							id: 'mark-unread',
							icon: iconItemMarkUnread,
							label: __( 'Mark Unread', 'omniform' ),
							isPrimary: true,
							isEligible: ( item ) => ! [ 'omniform_unread', 'publish' ].includes( item.status ),
							callback: ( items ) => onChangeStatus( items, 'omniform_unread' ),
						},
						{
							id: 'trash',
							label: __( 'Trash', 'omniform' ),
							icon: iconItemTrash,
							callback: ( items ) => onChangeStatus( items, 'trash' ),
							supportsBulk: true,
						},
					] }
					isItemClickable={ () => true }
					onClickItem={ ( item ) => console.debug( 'view', item ) }
				/>
			</Page>
			<Popover.Slot />
		</SlotFillProvider>
	);
}
