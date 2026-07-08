import { useNavigate } from 'react-router-dom';

/**
 * WordPress dependencies.
 */
import {
	__experimentalHeading as Heading,
	__experimentalHStack as HStack,
	__experimentalVStack as VStack,
	Button,
	Card,
	CardBody,
} from '@wordpress/components';
import { DataViews } from '@wordpress/dataviews';
import { useEntityRecords } from '@wordpress/core-data';
import { __ } from '@wordpress/i18n';
import { addQueryArgs } from '@wordpress/url';

/**
 * Internal dependencies.
 */
import senderEmailField from '../../fields/sender-email';
import senderGravatarField from '../../fields/sender-gravatar';
import formTitleField from '../../fields/form-title';
import relativeDateField from '../../fields/relative-date';

export default function DataViewLatestResponses() {
	const navigate = useNavigate();

	const fields = [
		senderEmailField,
		senderGravatarField,
		formTitleField,
		relativeDateField,
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
		<VStack spacing="4">
			<HStack>
				<Heading level={ 4 }>
					{ __( 'Latest Responses', 'omniform' ) }
				</Heading>
			</HStack>

			<Card>
				<CardBody>
					<DataViews
						data={ records || [] }
						isLoading={ isLoading }
						view={ {
							type: 'table',
							titleField: fields[ 0 ]?.id,
							mediaField: 'omniform_form.sender_gravatar',
							fields: fields
								.map( ( field ) => field.id )
								.filter(
									( id ) =>
										id !== fields[ 0 ]?.id &&
										id !== 'omniform_form.sender_gravatar',
								),
							layout: {
								enableMoving: false,
							},
						} }
						fields={ fields }
						paginationInfo={ { totalItems, totalPages } }
						defaultLayouts={ { table: {} } }
						onClickItem={ ( item ) =>
							( document.location.href = addQueryArgs( 'post.php', {
								post: item.id,
								action: 'edit',
							} ) )
						}
					>
						<DataViews.Layout />
					</DataViews>
				</CardBody>
			</Card>

			<HStack justify="right">
				<Button
					variant="link"
					onClick={ () => navigate( '/responses' ) }
				>
					{ __( 'View all responses', 'omniform' ) }
				</Button>
			</HStack>
		</VStack>
	);
}
