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
	iconInfo,
} from '../shared/icons';

const variations = [
	{
		name: 'info-response-notification',
		icon: iconInfo,
		title: __( 'Info Message', 'omniform' ),
		description: __( 'Provides users with helpful or relevant information.', 'omniform' ),
		attributes: {
			messageContent: __( 'Sharing some info you might find helpful.', 'omniform' ),
		},
	},
	{
		name: 'success-response-notification',
		icon: iconSuccess,
		title: __( 'Success Notification', 'omniform' ),
		description: __( 'Notifies user of successful submission.', 'omniform' ),
		attributes: {
			messageContent: __( 'Success! Your submission has been completed.', 'omniform' ),
			className: 'is-style-success',
		},
	},
	{
		name: 'error-response-notification',
		icon: iconError,
		title: __( 'Error Notification', 'omniform' ),
		description: __( 'Notifies user of failed submission.', 'omniform' ),
		attributes: {
			messageContent: __( 'Unfortunately, your submission was not successful. Please ensure all fields are correctly filled out and try again.', 'omniform' ),
			className: 'is-style-error',
		},
	},
];

variations.forEach( ( variation ) => {
	variation.isActive = ( blockAttributes, variationAttributes ) => {
		if ( ! blockAttributes?.className || blockAttributes.className.includes( 'is-style-info' ) ) {
			return variation.name === 'info-response-notification';
		}

		return blockAttributes.className.includes( variationAttributes.className );
	};

	if ( ! variation.scope ) {
		variation.scope = [ 'inserter', 'block' ];
	}
} );

export default variations;
