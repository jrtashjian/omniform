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
			transform( label ) {
				return createBlock( 'omniform/select-group', { label } );
			},
		},
	],
};

export default transforms;
