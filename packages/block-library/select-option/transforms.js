/**
 * WordPress dependencies
 */
import { createBlock } from '@wordpress/blocks';

const transforms = {
	from: [
		{
			type: 'prefix',
			prefix: '>',
			transform( fieldLabel ) {
				return createBlock( 'omniform/select-group', { fieldLabel } );
			},
		},
	],
};

export default transforms;
