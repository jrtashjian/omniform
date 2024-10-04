/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { cleanFieldName } from '../shared/utils';

export const createInputField = ( fieldLabel, type, isRequired, extra = {} ) => {
	const labelBlock = { name: 'omniform/label' };
	const inputBlock = { name: 'omniform/input', attributes: { fieldType: type || 'text', fieldValue: extra?.fieldValue || '' } };

	const layout = [ 'checkbox', 'radio' ].includes( type )
		? { type: 'flex', orientation: 'horizontal', justifyContent: 'left', flexWrap: 'nowrap', verticalAlignment: 'center' }
		: undefined;

	return ( {
		name: 'omniform/field',
		attributes: {
			fieldLabel,
			fieldName: extra?.fieldName || cleanFieldName( fieldLabel ).toLowerCase(),
			isRequired,
			layout,
		},
		innerBlocks: [ 'checkbox', 'radio' ].includes( type )
			? [ inputBlock, labelBlock ]
			: [ labelBlock, inputBlock ],
	} );
};

export const createSelectBlock = ( fieldLabel, options, isRequired, extra = {} ) => {
	const labelBlock = { name: 'omniform/label' };
	const inputBlock = { name: 'omniform/select', attributes: { fieldPlaceholder: __( 'Select One', 'omniform' ), isMultiple: false, fieldValue: extra?.fieldValue || '' } };

	inputBlock.innerBlocks = options.map( ( option ) => ( {
		name: 'omniform/select-option',
		attributes: { fieldLabel: option },
	} ) );

	return ( {
		name: 'omniform/field',
		attributes: {
			fieldLabel,
			fieldName: extra?.fieldName || cleanFieldName( fieldLabel ).toLowerCase(),
			isRequired,
		},
		innerBlocks: [ labelBlock, inputBlock ],
	} );
};

export const createTextareaField = ( fieldLabel, isRequired, extra = {} ) => {
	const labelBlock = { name: 'omniform/label' };
	const textareaBlock = { name: 'omniform/textarea', attributes: { fieldValue: extra?.fieldValue || '' } };

	return ( {
		name: 'omniform/field',
		attributes: {
			fieldLabel,
			fieldName: extra?.fieldName || cleanFieldName( fieldLabel ).toLowerCase(),
			isRequired,
		},
		innerBlocks: [ labelBlock, textareaBlock ],
	} );
};
