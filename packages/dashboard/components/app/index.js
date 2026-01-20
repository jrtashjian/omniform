/**
 * WordPress dependencies.
 */
import { useState, useMemo } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';
import {
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
import {
	iconItemMarkRead,
	iconItemMarkUnread,
	iconItemTrash,
	iconItemView,
} from '../../../block-library/shared/icons';

const STATUS_CONFIG = {
	publish: {
		display: __( 'Unread', 'omniform' ),
	},
	omniform_unread: {
		display: __( 'Unread', 'omniform' ),
		action: __( 'Marked as unread', 'omniform' ),
		error: __( 'marking as unread', 'omniform' ),
	},
	omniform_read: {
		display: __( 'Read', 'omniform' ),
		action: __( 'Marked as read', 'omniform' ),
		error: __( 'marking as read', 'omniform' ),
	},
	omniform_spam: {
		display: __( 'Spam', 'omniform' ),
		action: __( 'Marked as spam', 'omniform' ),
		error: __( 'marking as spam', 'omniform' ),
	},
	trash: {
		display: __( 'Trashed', 'omniform' ),
		action: __( 'Moved to trash', 'omniform' ),
		error: __( 'moving to trash', 'omniform' ),
	},
};

export default function App() {
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
		},
		{
			id: 'status',
			label: __( 'Status', 'omniform' ),
			render: ( { item } ) => STATUS_CONFIG[ item.status ]?.display || item.status,
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
			status: [ 'publish', 'omniform_unread', 'omniform_read', 'omniform_spam' ],
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
		try {
			for ( const item of items ) {
				await editEntityRecord( 'postType', item.type, item.id, { status } );
				await saveEditedEntityRecord( 'postType', item.type, item.id, { throwOnError: true } );
			}
			/* translators: %s: action description */
			createSuccessNotice( sprintf( __( '%s successfully.', 'omniform' ), STATUS_CONFIG[ status ].action ), { type: 'snackbar' } );
		} catch ( error ) {
			/* translators: %s: action description */
			createErrorNotice( sprintf( __( 'Error %s.', 'omniform' ), STATUS_CONFIG[ status ].error ), { type: 'snackbar' } );
		}
	}

	return (
		<>
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
							// eslint-disable-next-line no-unused-vars
							callback: ( items ) => {},
						},
						{
							id: 'mark-read',
							icon: iconItemMarkRead,
							label: __( 'Mark Read', 'omniform' ),
							isPrimary: true,
							isEligible: ( item ) => item.status !== 'omniform_read' && item.status !== 'omniform_spam',
							callback: ( items ) => onChangeStatus( items, 'omniform_read' ),
							supportsBulk: true,
						},
						{
							id: 'mark-unread',
							icon: iconItemMarkUnread,
							label: __( 'Mark Unread', 'omniform' ),
							isPrimary: true,
							isEligible: ( item ) => item.status === 'omniform_read' && item.status !== 'omniform_spam',
							callback: ( items ) => onChangeStatus( items, 'omniform_unread' ),
							supportsBulk: true,
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
					// eslint-disable-next-line no-unused-vars
					onClickItem={ ( item ) => {} }
				/>
			</Page>
		</>
	);
}
