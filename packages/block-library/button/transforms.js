/**
 * WordPress dependencies
 */
import { createBlock } from '@wordpress/blocks';

const transforms = {
	from: [
		{
			type: 'block',
			blocks: [ 'jetpack/button' ],
			transform: ( attributes, innerBlocks ) => {
				return createBlock( 'omniform/button', {
					buttonLabel: attributes?.text,
					buttonType: 'submit',
				}, innerBlocks );
			},
		},
	],
};

export default transforms;
