/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	fieldHidden,
} from '../shared/icons';

const variations = [
	{
		name: 'field-current-user-id',
		icon: fieldHidden,
		title: __( 'Current User ID', 'omniform' ),
		description: __( 'Hidden input field to store the current user ID.', 'omniform' ),
		attributes: { fieldName: 'get_current_user_id', fieldValue: '{{ get_current_user_id }}' },
	},
	{
		name: 'field-search-query',
		icon: fieldHidden,
		title: __( 'Search Query', 'omniform' ),
		description: __( 'Hidden input field to store the search query.', 'omniform' ),
		attributes: { fieldName: 'get_search_query', fieldValue: '{{ get_search_query }}' },
	},
	{
		name: 'field-user-locale',
		icon: fieldHidden,
		title: __( 'User Locale', 'omniform' ),
		description: __( 'Hidden input field to store the user locale.', 'omniform' ),
		attributes: { fieldName: 'get_user_locale', fieldValue: '{{ get_user_locale }}' },
	},
];

variations.forEach( ( variation ) => {
	variation.isActive = ( blockAttributes, variationAttributes ) =>
		blockAttributes.fieldName === variationAttributes.fieldName &&
		blockAttributes.fieldValue === variationAttributes.fieldValue;

	if ( ! variation.scope ) {
		variation.scope = [ 'inserter', 'block', 'transform' ];
	}
} );

export default variations;
