/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

const variations = [
	{
		name: 'button-submit',
		title: __( 'Submit', 'omniform' ),
		description: __( 'Trigger form submission.', 'omniform' ),
		attributes: {
			buttonType: 'submit',
			buttonLabel: __( 'Submit', 'omniform' ),
		},
		isDefault: true,
	},
	{
		name: 'button-reset',
		title: __( 'Reset', 'omniform' ),
		description: __( 'Revert form to default state.', 'omniform' ),
		attributes: {
			buttonType: 'reset',
			buttonLabel: __( 'Reset', 'omniform' ),
		},
	},
];

variations.forEach( ( variation ) => {
	variation.isActive = ( blockAttributes, variationAttributes ) =>
		blockAttributes.buttonType ===
		variationAttributes.buttonType;

	if ( ! variation.scope ) {
		variation.scope = [ 'inserter', 'block', 'transform' ];
	}
} );

export default variations;
