/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';

function SenderGravatarField( { item } ) {
	return (
		<img
			alt={ __( 'Sender avatar', 'omniform' ) }
			src={ item.omniform_form.sender_gravatar }
			style={ { width: '40px', height: '40px' } }
		/>
	);
}

const senderGravatarField = {
	id: 'omniform_form.sender_gravatar',
	label: __( 'Avatar', 'omniform' ),
	isVisible: () => false,
	render: SenderGravatarField,
	enableHiding: false,
	enableSorting: false,
	filterBy: false,
};

export default senderGravatarField;
