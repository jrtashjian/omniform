/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

const variations = [
	{
		name: 'button-submit',
		title: __( 'Submit', 'omniform' ),
		description: __( '', 'omnigroup' ),
		attributes: {
			buttonType: 'submit',
			buttonLabel: __( 'Submit', 'omniform' ),
		},
	},
	{
		name: 'button-reset',
		title: __( 'Reset', 'omniform' ),
		description: __( '', 'omnigroup' ),
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
} );

export default variations;
