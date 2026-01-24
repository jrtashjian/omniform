/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';
import { addQueryArgs } from '@wordpress/url';
import {
	__experimentalHStack as HStack,
	Button,
	Icon,
} from '@wordpress/components';
import {
	drafts as draftsIcon,
	scheduled as scheduledIcon,
	pending as pendingIcon,
	notAllowed as notAllowedIcon,
	published as publishedIcon,
} from '@wordpress/icons';

/**
 * Internal dependencies.
 */
import PostTypeDataView from '../post-type-data-view';

export default function FormList() {
	const postStatuses = useSelect( ( select ) => select( coreStore ).getStatuses(), [] );
	const iconMapping = {
		draft: draftsIcon,
		future: scheduledIcon,
		pending: pendingIcon,
		private: notAllowedIcon,
		publish: publishedIcon,
	};

	const fields = [
		{
			id: 'title',
			label: __( 'Form', 'omniform' ),
			render: ( { item } ) => item.title.rendered,
			enableHiding: false,
			filterBy: false,
		},
		{
			id: 'status',
			label: __( 'Status', 'omniform' ),
			render: ( { item } ) => {
				const statusName = postStatuses?.find( ( s ) => s.slug === item.status )?.name || item.status;
				return (
					<HStack gap="2" justify="start">
						<Icon icon={ iconMapping[ item.status ] } style={ { fill: 'currentColor' } } />
						<span>{ statusName }</span>
					</HStack>
				);
			},
			type: 'text',
			enableHiding: false,
			filterBy: false,
		},
	];

	const actions = [
		{
			id: 'edit',
			label: __( 'Edit', 'omniform' ),
			callback: ( items ) => document.location.href = addQueryArgs( 'post.php', { post: items[ 0 ].id, action: 'edit' } ),
			isPrimary: true,
		},
		{
			id: 'view',
			label: __( 'View', 'omniform' ),
			callback: ( items ) => document.location.href = addQueryArgs( '/', { post_type: 'omniform', p: items[ 0 ].id } ),
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
				onClick={ () => document.location.href = addQueryArgs( 'post-new.php', { post_type: 'omniform' } ) }
			>
				{ __( 'Create Form', 'omniform' ) }
			</Button>
		</>
	);

	return (
		<PostTypeDataView
			pageTitle={ __( 'Forms', 'omniform' ) }
			pageActions={ pageActions }
			fields={ fields }
			actions={ actions }
			postType="omniform"
			filterStatuses={ [ 'publish', 'draft', 'trash' ] }
			initialSortField="modified"
			onClickItem={ ( item ) => document.location.href = addQueryArgs( 'post.php', { post: item.id, action: 'edit' } ) }
		/>
	);
}
