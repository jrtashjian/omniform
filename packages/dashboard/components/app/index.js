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
import { useEntityRecords } from '@wordpress/core-data';

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
		},
		{
			id: 'omniform_form.title',
			label: __( 'Form', 'omniform' ),
			render: ( { item } ) => (
				<Button href={ item.omniform_form.form_edit_url } size="compact" style={ { position: 'relative', left: '-12px' } }>
					{ item.omniform_form.title }
				</Button>
			),
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

	const titleField = 'sender';

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
		};
	}, [ view ] );

	const {
		records = [],
		isResolving: isLoadingData,
		totalItems,
		totalPages,
	} = useEntityRecords( 'postType', 'omniform_response', queryArgs );

	return (
		<SlotFillProvider>
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
							label: __( 'View', 'omniform' ),
							isPrimary: true,
							callback: ( items ) => console.debug( 'view', items ),
						},
						{
							id: 'trash',
							label: __( 'Trash', 'omniform' ),
							isPrimary: true,
							callback: ( items ) => console.debug( 'trash', items ),
							supportsBulk: true,
						},
					] }
				/>
			</Page>
			<Popover.Slot />
		</SlotFillProvider>
	);
}
