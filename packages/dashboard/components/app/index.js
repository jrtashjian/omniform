/**
 * WordPress dependencies.
 */
import { useState, useMemo } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import {
	Popover,
	SlotFillProvider,
	__experimentalHeading as Heading,
	__experimentalHStack as HStack,
	__experimentalVStack as VStack,
	Button,
	Icon,
} from '@wordpress/components';
import {
	FullscreenMode,
	InterfaceSkeleton,
} from '@wordpress/interface';

import { DataViews } from '@wordpress/dataviews/wp';
import { useEntityRecords } from '@wordpress/core-data';

import { Path, SVG } from '@wordpress/primitives';
export const logo = (
	<SVG viewBox="0 0 24 24">
		<Path fill="#D92E83" fillRule="evenodd" d="M3.33 15.424a4.842 4.842 0 0 1 0-6.848l.207-.208v-2.42a2.421 2.421 0 0 1 2.421-2.422H8.38l.086-.086a4.842 4.842 0 0 1 6.848 0l.086.086h2.665a2.421 2.421 0 0 1 2.421 2.421v2.665a4.842 4.842 0 0 1 0 6.776v2.665a2.421 2.421 0 0 1-2.421 2.42h-2.665l-.086.087a4.842 4.842 0 0 1-6.848 0l-.086-.086H5.96a2.421 2.421 0 0 1-2.422-2.421v-2.421l-.207-.208ZM12 5a7 7 0 0 1 7 7h-1.604A5.396 5.396 0 0 0 12 6.604V5Zm0 12.396V19a7 7 0 0 1-7-7h1.604A5.396 5.396 0 0 0 12 17.396ZM15.5 12A3.5 3.5 0 0 0 12 8.5v1.896c.886 0 1.604.718 1.604 1.604H15.5Zm-5.104 0c0 .886.718 1.604 1.604 1.604V15.5A3.5 3.5 0 0 1 8.5 12h1.896Z" clipRule="evenodd" />
	</SVG>
);

export default function App( { settings } ) {
	const fields = [
		{
			id: 'id',
			label: __( 'ID', 'omniform' ),
			type: 'number',
		},
		{
			id: 'date',
			label: __( 'Date', 'omniform' ),
			type: 'date',
			enableHiding: false,
		},
		{
			id: 'omniform_form.title',
			label: __( 'Form', 'omniform' ),
			type: 'text',
			enableHiding: false,
		},
	];

	const titleField = 'id';

	const [ view, setView ] = useState( {
		type: 'table',
		search: '',
		page: 1,
		perPage: 2,
		sort: {
			field: 'id',
			direction: 'desc',
		},
		titleField,
		fields: fields.map( ( field ) => field.id ).filter( ( id ) => id !== titleField ),
		layout: {},
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
			<FullscreenMode isActive={ false } />
			<InterfaceSkeleton
				header={ (
					<VStack className="omniform-page__header" as="header">
						<HStack justify="space-between" spacing={ 2 }>
							<HStack spacing={ 2 } justify="left">
								<Icon icon={ logo } size={ 24 } />
								<Heading as="h2" level={ 3 } weight={ 500 } truncate>
									{ __( 'OmniForm', 'omniform' ) }
								</Heading>
							</HStack>
							<HStack
								style={ { width: 'auto', flexShrink: 0 } }
								spacing={ 2 }
								className="omniform-page__header-actions"
							>
								<Button variant="primary">
									{ __( 'Primary Action', 'omniform' ) }
								</Button>
							</HStack>
						</HStack>
						<p className="omniform-page__header-subtitle">
							{ __( 'Subtitle', 'omniform' ) }
						</p>
					</VStack>
				) }
				content={ (
					<>
						<DataViews
							data={ records || [] }
							isLoading={ isLoadingData }
							view={ view }
							onChangeView={ setView }
							fields={ fields }
							paginationInfo={ { totalItems, totalPages } }
							defaultLayouts={ { table: {} } }
						/>
					</>
				) }
			/>
			<Popover.Slot />
		</SlotFillProvider>
	);
}
