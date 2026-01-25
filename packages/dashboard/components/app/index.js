/**
 * WordPress dependencies.
 */
import { useState } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';
import {
	__experimentalHStack as HStack,
	__experimentalVStack as VStack,
	Button,
	Card,
	CardBody,
	CardDivider,
} from '@wordpress/components';
import { Page } from '@wordpress/admin-ui';
// import { DataViews } from '@wordpress/dataviews/wp';
// import {
// 	useEntityRecords,
// 	store as coreStore,
// } from '@wordpress/core-data';
import { close } from '@wordpress/icons';
// import { useDispatch } from '@wordpress/data';
// import { store as noticesStore } from '@wordpress/notices';
import { EditorSnackbars } from '@wordpress/editor';

/**
 * Internal dependencies.
 */
import FormList from '../form-list';
import ResponseList from '../response-list';

// const STATUS_CONFIG = {
// 	publish: {
// 		display: __( 'Unread', 'omniform' ),
// 	},
// 	omniform_unread: {
// 		display: __( 'Unread', 'omniform' ),
// 		action: __( 'Marked as unread', 'omniform' ),
// 		error: __( 'marking as unread', 'omniform' ),
// 	},
// 	omniform_read: {
// 		display: __( 'Read', 'omniform' ),
// 		action: __( 'Marked as read', 'omniform' ),
// 		error: __( 'marking as read', 'omniform' ),
// 	},
// 	omniform_spam: {
// 		display: __( 'Spam', 'omniform' ),
// 		action: __( 'Marked as spam', 'omniform' ),
// 		error: __( 'marking as spam', 'omniform' ),
// 	},
// 	trash: {
// 		display: __( 'Trashed', 'omniform' ),
// 		action: __( 'Moved to trash', 'omniform' ),
// 		error: __( 'moving to trash', 'omniform' ),
// 	},
// };

export default function App( { settings } ) {
	const [ activeItem, setActiveItem ] = useState( null );

	// // Change Status Action
	// const { editEntityRecord, saveEditedEntityRecord } = useDispatch( coreStore );
	// const { createSuccessNotice, createErrorNotice } = useDispatch( noticesStore );

	// async function onChangeStatus( items, status ) {
	// 	try {
	// 		for ( const item of items ) {
	// 			await editEntityRecord( 'postType', item.type, item.id, { status } );
	// 			await saveEditedEntityRecord( 'postType', item.type, item.id, { throwOnError: true } );
	// 		}
	// 		/* translators: %s: action description */
	// 		createSuccessNotice( sprintf( __( '%s successfully.', 'omniform' ), STATUS_CONFIG[ status ].action ), { type: 'snackbar' } );
	// 	} catch ( error ) {
	// 		/* translators: %s: action description */
	// 		createErrorNotice( sprintf( __( 'Error %s.', 'omniform' ), STATUS_CONFIG[ status ].error ), { type: 'snackbar' } );
	// 	}
	// }

	return (
		<>
			<EditorSnackbars />

			<div className="omniform-layout">
				<div className="omniform-layout__container">
					<div className="omniform-layout__content">
						{ settings.screen === 'dashboard' && (
							<Page title={ __( 'OmniForm', 'omniform' ) } />
						) }

						{ settings.screen === 'forms' && (
							<FormList />
						) }

						{ settings.screen === 'responses' && (
							<ResponseList setActiveItem={ setActiveItem } />
						) }

						{ /* <Page
							title={ __( 'Responses', 'omniform' ) }
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
										callback: ( items ) => setActiveItem( items[ 0 ] ),
									},
									{
										id: 'mark-read',
										icon: iconItemMarkRead,
										label: __( 'Mark Read', 'omniform' ),
										isPrimary: true,
										isEligible: ( item ) => item.status !== 'omniform_read' && item.status !== 'omniform_spam',
										callback: ( items ) => onChangeStatus( items, 'omniform_read' ),
									},
									{
										id: 'mark-unread',
										icon: iconItemMarkUnread,
										label: __( 'Mark Unread', 'omniform' ),
										isPrimary: true,
										isEligible: ( item ) => item.status === 'omniform_read' && item.status !== 'omniform_spam',
										callback: ( items ) => onChangeStatus( items, 'omniform_unread' ),
									},
									{
										id: 'trash',
										label: __( 'Trash', 'omniform' ),
										icon: iconItemTrash,
										callback: ( items ) => onChangeStatus( items, 'trash' ),
									},
								] }
								isItemClickable={ () => true }
								onClickItem={ ( item ) => setActiveItem( item ) }
							/>
						</Page> */ }
					</div>

					<div className="omniform-layout__panel">
						{ activeItem && (
							<Page
								title={ __( 'Response Details', 'omniform' ) }
								actions={
									<>
										<Button
											icon={ close }
											label={ __( 'Close panel', 'omniform' ) }
											onClick={ () => setActiveItem( null ) }
										/>
									</>
								}
							>
								<Card>
									<CardBody>
										<HStack>
											<HStack alignment="left">
												<div className="field__avatar">
													<img
														alt={ __( 'Author avatar' ) }
														src={ activeItem.omniform_form.sender_gravatar }
													/>
												</div>
												<span className="field__email">
													{ activeItem.omniform_form.sender_email }
												</span>
											</HStack>
											<VStack alignment="right" spacing={ 0 } style={ { flexShrink: 0 } }>
												<span>{ ( new Date( activeItem.date ) ).toLocaleDateString( 'en-US', { month: 'long', day: 'numeric', year: 'numeric' } ) }</span>
												<span>at { ( new Date( activeItem.date ) ).toLocaleTimeString( 'en-US', { hour: 'numeric', minute: '2-digit', hour12: true } ).toLowerCase() }</span>
											</VStack>
										</HStack>
									</CardBody>

									<CardDivider />

									<CardBody>
										<pre>{ JSON.stringify( activeItem, null, 2 ) }</pre>
									</CardBody>
								</Card>
							</Page>
						) }
					</div>
				</div>
			</div>
		</>
	);
}
