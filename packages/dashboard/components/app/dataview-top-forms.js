/**
 * WordPress dependencies.
 */
import {
	__experimentalHeading as Heading,
	Card,
	CardBody,
	CardHeader,
	Notice,
} from '@wordpress/components';
import {
	titleField,
} from '@wordpress/fields';
import { DataViews } from '@wordpress/dataviews/wp';
import { useMemo, useState, useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { addQueryArgs } from '@wordpress/url';
import apiFetch from '@wordpress/api-fetch';

export default function DataViewTopForms( { period } ) {
	const [ isLoading, setIsLoading ] = useState( null );
	const [ data, setData ] = useState( null );
	const [ error, setError ] = useState( null );

	const fields = useMemo( () => [
		{
			...titleField,
			enableSorting: false,
			enableHiding: false,
			filterBy: false,
		},
		{
			id: 'unique_responses',
			label: __( 'Responses', 'omniform' ),
			enableSorting: false,
			enableHiding: false,
			filterBy: false,
			type: 'integer',
		},
		{
			id: 'unique_impressions',
			label: __( 'Impressions', 'omniform' ),
			enableSorting: false,
			enableHiding: false,
			filterBy: false,
			type: 'integer',
		},
		{
			id: 'conversion_rate',
			label: __( 'Conversion Rate', 'omniform' ),
			enableSorting: false,
			enableHiding: false,
			filterBy: false,
			render: ( { item } ) => {
				const rate = item.conversion_rate || 0;
				return `${ ( rate * 100 ).toFixed( 1 ) }%`;
			},
		},
	], [] );

	const defaultView = useMemo( () => ( {
		type: 'table',
		titleField: fields[ 0 ]?.id,
		fields: fields.map( ( field ) => field.id ).filter( ( id ) => id !== fields[ 0 ]?.id ),
		layout: {
			enableMoving: false,
		},
	} ), [ fields ] );

	const [ view, setView ] = useState( defaultView );

	useEffect( () => {
		const fetchData = async () => {
			setIsLoading( true );
			setError( null );

			try {
				const path = addQueryArgs( '/omniform/v1/analytics/top-forms', { period } );
				const response = await apiFetch( { path } );
				setData( response );
				setIsLoading( false );
			} catch ( err ) {
				setError( err );
				setIsLoading( false );
			}
		};

		fetchData();
	}, [ period ] );

	return (
		<Card isBorderless>
			<CardHeader>
				<Heading level={ 2 }>{ __( 'Top Performing Forms', 'omniform' ) }</Heading>
			</CardHeader>
			<CardBody>
				{ error ? (
					<Notice status="warning" isDismissible={ false }>{ __( 'An error occurred while fetching data.', 'omniform' ) }</Notice>
				) : (
					<DataViews
						data={ data?.forms || [] }
						isLoading={ isLoading }
						view={ view }
						onChangeView={ setView }
						fields={ fields }
						paginationInfo={ { totalItems: 0, totalPages: 0 } }
						defaultLayouts={ { table: {} } }
						onClickItem={ ( item ) => document.location.href = addQueryArgs( 'post.php', { post: item.id, action: 'edit' } ) }
					>
						<DataViews.Layout />
					</DataViews>
				) }
			</CardBody>
		</Card>
	);
}
