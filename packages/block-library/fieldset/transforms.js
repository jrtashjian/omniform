/**
 * WordPress dependencies
 */
import { createBlock } from '@wordpress/blocks';

const transforms = {
	from: [
		{
			type: 'block',
			blocks: [
				'jetpack/field-radio',
				'jetpack/field-checkbox-multiple',
			],
			transform: ( attributes, innerBlocks ) => {
				return createBlock( 'omniform/fieldset', {
					fieldLabel: attributes?.label,
					isRequired: attributes?.required,
				}, innerBlocks );
			},
		},
	],
};

export default transforms;
