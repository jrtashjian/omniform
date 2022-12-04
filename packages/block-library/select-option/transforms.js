/**
 * WordPress dependencies
 */
import { createBlock, getBlockAttributes } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import { name } from './block.json';

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
