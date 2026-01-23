/**
 * WordPress dependencies.
 */
import { Page } from '@wordpress/admin-ui';
import {
	__experimentalHStack as HStack,
	Button,
} from '@wordpress/components';
import { useEntityRecords } from '@wordpress/core-data';
import { useSelect } from '@wordpress/data';
import { DataViews } from '@wordpress/dataviews/wp';
import { useMemo, useState } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';

/**
 * Abbreviates a number for display, e.g., 19545 becomes '19.5k'.
 *
 * @param {number} num The number to abbreviate.
 * @return {string} The abbreviated number.
 */
const abbreviateNumber = ( num ) => {
	if ( num < 1000 ) {
		return num.toString();
	}
	const suffixes = [ '', 'k', 'M', 'B', 'T' ];
	let suffixIndex = 0;
	let value = num;
	while ( value >= 1000 && suffixIndex < suffixes.length - 1 ) {
		value /= 1000;
		suffixIndex++;
	}
	return value.toFixed( 1 ).replace( /\.0$/, '' ) + suffixes[ suffixIndex ];
};

export default function PostTypeDataView( {
	pageTitle,
	fields,
	actions,
	postType,
	titleField = fields[ 0 ]?.id,
	initialSortField = 'date',
	initialSortDirection = 'desc',
	initialPerPage = 10,
	statuses = [ 'draft', 'future', 'pending', 'private', 'publish' ],
	filterStatuses = [],
	onClickItem = () => {},
} ) {
	const postStatuses = useSelect( ( select ) => select( 'core' ).getStatuses(), [] );

	const itemCounts = useSelect( ( select ) => {
		const all = select( 'core' ).getEntityRecordsTotalItems( 'postType', postType, { status: statuses } );
		const statusCounts = filterStatuses.reduce( ( acc, status ) => {
			acc[ status ] = select( 'core' ).getEntityRecordsTotalItems( 'postType', postType, { status: [ status ] } );
			return acc;
		}, {} );
		return { all, ...statusCounts };
	}, [ postType, statuses, filterStatuses ] );

	const defaultView = useMemo( () => ( {
		type: 'table',
		search: '',
		page: 1,
		perPage: initialPerPage,
		sort: {
			field: initialSortField,
			direction: initialSortDirection,
		},
		titleField,
		fields: fields.map( ( field ) => field.id ).filter( ( id ) => id !== titleField ),
		layout: {
			enableMoving: false,
		},
		filters: [
			{
				field: 'status',
				operator: 'isAny',
				value: statuses,
			},
		],
	} ), [ fields, titleField, initialSortField, initialSortDirection, initialPerPage, statuses ] );

	const [ view, setView ] = useState( defaultView );

	const queryArgs = useMemo( () => {
		const filters = {};

		view.filters.forEach( ( filter ) => {
			if ( filter.field === 'status' && filter.operator === 'isAny' ) {
				filters.status = filter.value;
			}
		} );

		return {
			per_page: view.perPage,
			page: view.page,
			order: view.sort?.direction,
			orderby: view.sort?.field,
			...filters,
		};
	}, [ view ] );

	const {
		records = [],
		isResolving: isLoadingData,
		totalItems,
		totalPages,
	} = useEntityRecords( 'postType', postType, queryArgs );

	return (
		<Page title={ pageTitle }>
			<DataViews
				data={ records || [] }
				isLoading={ isLoadingData }
				view={ view }
				onChangeView={ setView }
				fields={ fields }
				actions={ actions }
				paginationInfo={ { totalItems, totalPages } }
				defaultLayouts={ { table: {} } }
				onClickItem={ ( item ) => onClickItem( item ) }
			>
				<HStack
					alignment="top"
					justify="space-between"
					className="dataviews__view-actions"
					spacing={ 1 }
				>
					<HStack
						justify="start"
						expanded={ false }
					>
						<Button size="compact" onClick={ () => setView( { ...view, filters: defaultView.filters } ) }>
							{ itemCounts.all
								? sprintf(
									/* translators: %s: Total number of items. */
									__( 'All (%s)', 'omniform' ),
									abbreviateNumber( itemCounts.all )
								)
								: __( 'All', 'omniform' )
							}
						</Button>
						{ filterStatuses.map( ( status ) => (
							<Button
								key={ status }
								size="compact"
								onClick={ () => setView( { ...view, filters: [ { field: 'status', operator: 'isAny', value: [ status ] } ] } ) }
							>
								{ ( postStatuses?.find( ( s ) => s.slug === status )?.name || status ) + ( itemCounts[ status ] ? ` (${ abbreviateNumber( itemCounts[ status ] ) })` : '' ) }
							</Button>
						) ) }
					</HStack>
					<HStack
						spacing={ 1 }
						expanded={ false }
						style={ { flexShrink: 0 } }
					>
						<DataViews.ViewConfig />
					</HStack>
				</HStack>
				<DataViews.Layout />
				<DataViews.Footer />
			</DataViews>
		</Page>
	);
}
