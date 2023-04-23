/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useDispatch, useSelect } from '@wordpress/data';
import { useMemo } from '@wordpress/element';
import { store as coreStore } from '@wordpress/core-data';
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
	}, [ forms ] );

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
