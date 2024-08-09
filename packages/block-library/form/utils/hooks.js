/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useDispatch, useSelect } from '@wordpress/data';
import { useMemo } from '@wordpress/element';
import {
	useEntityProp,
	store as coreStore,
} from '@wordpress/core-data';
import { serialize } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import { POST_TYPE } from '../../shared/constants';

/**
 * Retrieves the available forms.
 *
 * @param {string} excludedId Form ID to exclude.
 *
 * @return {{ forms: Array, isResolving: boolean }} array of forms.
 */
export function useAlternativeForms( excludedId ) {
	const { forms = [], isResolving } = useSelect( ( select ) => {
		const { getEntityRecords, isResolving: _isResolving } = select( coreStore );
		const queryPublished = { per_page: -1, status: 'publish' };
		const queryDrafts = { per_page: -1, status: 'draft' };

		return {
			forms: [
				...getEntityRecords( 'postType', POST_TYPE, queryPublished ) ?? [],
				...getEntityRecords( 'postType', POST_TYPE, queryDrafts ) ?? [],
			],
			isLoading: _isResolving( 'getEntityRecords', [ 'postType', POST_TYPE, queryPublished ] ) || _isResolving( 'getEntityRecords', [ 'postType', POST_TYPE, queryDrafts ] ),
		};
	}, [] );

	const filteredForms = useMemo( () => {
		if ( ! forms ) {
			return [];
		}
		return forms.filter( ( form ) => form.id !== excludedId ) || [];
	}, [ forms, excludedId ] );

	return {
		forms: filteredForms,
		isResolving,
	};
}

export function useCreateFormFromBlocks( setAttributes ) {
	const { saveEntityRecord } = useDispatch( coreStore );

	return async ( blocks = [], title = __( 'Untitled Form', 'omniform' ), type = 'uncategorized' ) => { // eslint-disable-line no-unused-vars
		const record = {
			title,
			content: serialize( blocks ),
			status: 'publish',
		};
		const form = await saveEntityRecord(
			'postType',
			POST_TYPE,
			record
		);
		setAttributes( { ref: form.id } );
	};
}

/**
 * Retrieves the form settings.
 *
 * @param {number} formId Form ID.
 *
 * @return {Object} Form settings.
 */
export function useStandardFormSettings( formId ) {
	const [ meta, setMeta ] = useEntityProp( 'postType', POST_TYPE, 'meta', formId );

	const [ formTitle, setFormTitle ] = useEntityProp( 'postType', POST_TYPE, 'title', formId );
	const [ formType, setFormType ] = useEntityProp( 'postType', POST_TYPE, 'omniform_type', formId );

	/**
	 * Retrieves the setting value.
	 *
	 * @param {string} key Setting key.
	 * @return {*} Setting value.
	 */
	const getSetting = ( key ) => {
		switch ( key ) {
			case 'form_type':
				return formType;
			case 'form_title':
				return formTitle;
			default:
				return meta?.[ key ];
		}
	};

	/**
	 * Sets the setting value.
	 *
	 * @param {string} key   Setting key.
	 * @param {*}      value Setting value.
	 */
	const setSetting = ( key, value ) => {
		switch ( key ) {
			case 'form_type':
				setFormType( value );

				// Reset the meta values when switching from the custom form type.
				if ( value !== 'custom' ) {
					setMeta( {
						...meta,
						submit_method: '',
						submit_action: '',
					} );
				}

				break;
			case 'form_title':
				setFormTitle( value );
				break;
			default:
				setMeta( { ...meta, [ key ]: value } );
		}
	};

	return { getSetting, setSetting };
}

/**
 * Retrieves the form settings.
 *
 * @param {Object} blockObject Props.
 */
export function useStandaloneFormSettings( blockObject ) {
	const {
		attributes,
		setAttributes,
	} = blockObject;

	/**
	 * Retrieves the setting value.
	 *
	 * @param {string} key Setting key.
	 * @return {*} Setting value.
	 */
	const getSetting = ( key ) => attributes[ key ];

	/**
	 * Sets the setting value.
	 *
	 * @param {string} key   Setting key.
	 * @param {*}      value Setting value.
	 */
	const setSetting = ( key, value ) => {
		switch ( key ) {
			case 'form_type':
				setAttributes( { [ key ]: value } );

				// Reset the meta values when switching from the custom form type.
				if ( value !== 'custom' ) {
					setAttributes( {
						submit_method: undefined,
						submit_action: undefined,
					} );
				}

				break;
			default:
				setAttributes( { [ key ]: value } );
		}
	};

	return { getSetting, setSetting };
}
