/**
 * WordPress dependencies.
 */
import {
	__experimentalText as Text,
	__experimentalVStack as VStack,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';

function SenderEmailField( { item } ) {
	const email = item.omniform_form.sender_email;
	const ip = item.omniform_form.sender_ip;

	if ( ! email ) {
		return <Text>{ ip }</Text>;
	}

	return (
		<VStack spacing="1">
			<Text>{ email }</Text>
			<Text variant="muted">{ ip }</Text>
		</VStack>
	);
}

const senderEmailField = {
	id: 'omniform_form.sender_email',
	label: __( 'Sender', 'omniform' ),
	render: SenderEmailField,
	enableHiding: false,
	enableSorting: false,
	filterBy: false,
};

export default senderEmailField;
