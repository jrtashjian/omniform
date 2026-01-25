/**
 * WordPress dependencies.
 */
import { restorePost as restorePostField } from '@wordpress/fields';
import { store as coreStore } from '@wordpress/core-data';
import { select } from '@wordpress/data';

const restorePost = {
	...restorePostField,
	isEligible: ( item ) =>
		item.status === 'trash' &&
		select( coreStore ).canUser( 'update', { kind: 'postType', name: item.type, id: item.id } ),
};

export default restorePost;
