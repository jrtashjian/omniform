/**
 * WordPress dependencies
 */
import { createBlock } from '@wordpress/blocks';

const transformJetpackField = ( attributes, innerBlocks, fieldType = 'text' ) => {
	const isOptionInput = [ 'checkbox', 'radio' ].includes( fieldType );

	let blockName;
	switch ( fieldType ) {
		case 'textarea':
			blockName = 'omniform/textarea';
			break;
		case 'select':
			blockName = 'omniform/select';
			break;
		default:
			blockName = 'omniform/input';
	}

	const fieldLabel = createBlock( 'omniform/label', {}, [] );
	const fieldInput = createBlock(
		blockName,
		{
			fieldType,
			fieldPlaceholder: attributes?.placeholder || attributes?.toggleLabel,
			fieldValue: attributes?.defaultValue,
		},
		( attributes?.options || [] ).map( ( option ) =>
			createBlock( 'omniform/select-option', { fieldLabel: option } )
		)
	);

	return createBlock(
		'omniform/field',
		{
			fieldLabel: attributes?.label,
			isRequired: attributes?.required,
			className: isOptionInput ? 'is-style-inline' : '',
		},
		isOptionInput
			? [ fieldInput, fieldLabel ]
			: [ fieldLabel, fieldInput ],
	);
};

const transforms = {
	from: [
		{
			type: 'block',
			blocks: [
				'jetpack/field-text',
				'jetpack/field-name',
			],
			transform: ( attributes, innerBlocks ) => transformJetpackField( attributes, innerBlocks ),
		},
		{
			type: 'block',
			blocks: [ 'jetpack/field-email' ],
			transform: ( attributes, innerBlocks ) => transformJetpackField( attributes, innerBlocks, 'email' ),
		},
		{
			type: 'block',
			blocks: [ 'jetpack/field-url' ],
			transform: ( attributes, innerBlocks ) => transformJetpackField( attributes, innerBlocks, 'url' ),
		},
		{
			type: 'block',
			blocks: [ 'jetpack/field-date' ],
			transform: ( attributes, innerBlocks ) => transformJetpackField( attributes, innerBlocks, 'date' ),
		},
		{
			type: 'block',
			blocks: [ 'jetpack/field-telephone' ],
			transform: ( attributes, innerBlocks ) => transformJetpackField( attributes, innerBlocks, 'tel' ),
		},
		{
			type: 'block',
			blocks: [
				'jetpack/field-checkbox',
				'jetpack/field-option-checkbox',
			],
			transform: ( attributes, innerBlocks ) => transformJetpackField( attributes, innerBlocks, 'checkbox' ),
		},
		{
			type: 'block',
			blocks: [ 'jetpack/field-option-radio' ],
			transform: ( attributes, innerBlocks ) => transformJetpackField( attributes, innerBlocks, 'radio' ),
		},
		{
			type: 'block',
			blocks: [ 'jetpack/field-select' ],
			transform: ( attributes, innerBlocks ) => transformJetpackField( attributes, innerBlocks, 'select' ),
		},
		{
			type: 'block',
			blocks: [ 'jetpack/field-textarea' ],
			transform: ( attributes, innerBlocks ) => transformJetpackField( attributes, innerBlocks, 'textarea' ),
		},
	],
};

export default transforms;
