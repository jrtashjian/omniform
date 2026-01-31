/**
 * WordPress dependencies.
 */
import {
	__experimentalHeading as Heading,
	Card,
	CardBody,
	CardHeader,
} from '@wordpress/components';
import { dateField } from '@wordpress/fields';
import { DataViews } from '@wordpress/dataviews/wp';
import { useEntityRecords } from '@wordpress/core-data';
import { __ } from '@wordpress/i18n';
import { addQueryArgs } from '@wordpress/url';

/**
 * Internal dependencies.
 */
import trashPost from '../../actions/trash-post';
import permanentlyDeletePost from '../../actions/permanently-delete-post';
import restorePost from '../../actions/restore-post';

export default function DataViewLatestResponses() {
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
		dateField,
	];

	const defaultActions = [
		trashPost,
		permanentlyDeletePost,
		restorePost,
	];

	const actions = [
		{
			id: 'view',
			label: __( 'View', 'omniform' ),
			callback: ( items ) => console.debug( items[ 0 ] ),
			isPrimary: true,
			isEligible: ( item ) => item.status !== 'trash',
		},
	];

	const {
		records = [],
		isResolving: isLoading,
		totalItems,
		totalPages,
	} = useEntityRecords( 'postType', 'omniform_response', {
		per_page: 10,
		status: [ 'omniform_read', 'omniform_unread' ],
	} );

	return (
		<Card isBorderless>
			<CardHeader>
				<Heading level={ 2 }>{ __( 'Latest Responses', 'omniform' ) }</Heading>
			</CardHeader>
			<CardBody>
				<DataViews
					data={ records || [] }
					isLoading={ isLoading }
					view={ {
						type: 'table',
						titleField: fields[ 0 ]?.id,
						mediaField: 'omniform_form.sender_gravatar',
						fields: fields.map( ( field ) => field.id ).filter( ( id ) => id !== fields[ 0 ]?.id && id !== 'omniform_form.sender_gravatar' ),
						layout: {
							enableMoving: false,
						},
					} }
					fields={ fields }
					actions={ [ ...actions, ...defaultActions ] }
					paginationInfo={ { totalItems, totalPages } }
					defaultLayouts={ { table: {} } }
					onClickItem={ ( item ) => document.location.href = addQueryArgs( 'post.php', { post: item.id, action: 'edit' } ) }
				>
					<DataViews.Layout />
				</DataViews>
			</CardBody>
		</Card>
	);
}
