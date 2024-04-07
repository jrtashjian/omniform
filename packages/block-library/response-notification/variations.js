/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import {
	iconSuccess,
	iconError,
} from '../shared/icons';

const variations = [
	{
		name: 'success-response-notification',
		icon: iconSuccess,
		title: __( 'Success Notification', 'omniform' ),
		description: __( 'Notifies user of successful submission.', 'omniform' ),
		attributes: {
			messageType: 'success',
			messageContent: __( 'Success! Your submission has been completed.', 'omniform' ),
			style: { border: { left: { color: 'var(--wp--preset--color--vivid-green-cyan,#00d084)', width: '6px' } }, spacing: { padding: { top: '0.5em', bottom: '0.5em', left: '1.5em', right: '1.5em' } } },
		},
		isDefault: true,
	},
	{
		name: 'error-response-notification',
		icon: iconError,
		title: __( 'Error Notification', 'omniform' ),
		description: __( 'Notifies user of failed submission.', 'omniform' ),
		attributes: {
			messageType: 'error',
			messageContent: __( 'Unfortunately, your submission was not successful. Please ensure all fields are correctly filled out and try again.', 'omniform' ),
			style: { border: { left: { color: 'var(--wp--preset--color--vivid-red,#cf2e2e)', width: '6px' } }, spacing: { padding: { top: '0.5em', bottom: '0.5em', left: '1.5em', right: '1.5em' } } },
		},
	},
];

variations.forEach( ( variation ) => {
	variation.isActive = ( blockAttributes, variationAttributes ) =>
		blockAttributes.messageType ===
		variationAttributes.messageType;

	if ( ! variation.scope ) {
		variation.scope = [ 'inserter', 'block' ];
	}
} );

export default variations;
