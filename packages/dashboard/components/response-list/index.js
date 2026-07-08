/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { Button } from '@wordpress/components';

/**
 * Internal dependencies.
 */
import PostTypeDataView from '../post-type-data-view';
import senderEmailField from '../../fields/sender-email';
import senderGravatarField from '../../fields/sender-gravatar';
import formTitleField from '../../fields/form-title';
import statusField from '../../fields/status';
import relativeDateField from '../../fields/relative-date';

export default function ResponseList( { setActiveItem } ) {
	const fields = [
		senderEmailField,
		senderGravatarField,
		formTitleField,
		statusField,
		relativeDateField,
	];

	const actions = [
		{
			id: 'view',
			label: __( 'View', 'omniform' ),
			callback: ( items ) => setActiveItem( items[ 0 ] ),
			isPrimary: true,
			isEligible: ( item ) => item.status !== 'trash',
		},
	];

	const pageActions = (
		<>
			<Button variant="primary" onClick={ () => {} }>
				{ __( 'Export', 'omniform' ) }
			</Button>
		</>
	);

	return (
		<PostTypeDataView
			pageTitle={ __( 'Responses', 'omniform' ) }
			subTitle={ __( 'View all the responses submitted through your forms.', 'omniform' ) }
			pageActions={ pageActions }
			fields={ fields }
			actions={ actions }
			postType="omniform_response"
			statuses={ [ 'omniform_unread', 'omniform_read' ] }
			filterStatuses={ [ 'omniform_unread', 'trash' ] }
			mediaField="omniform_form.sender_gravatar"
			onClickItem={ ( item ) => setActiveItem( item ) }
		/>
	);
}
