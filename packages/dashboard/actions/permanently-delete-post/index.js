/**
 * WordPress dependencies.
 */
import { permanentlyDeletePost as permanentlyDeletePostField } from '@wordpress/fields';
import { store as coreStore } from '@wordpress/core-data';
import { select } from '@wordpress/data';

const permanentlyDeletePost = {
	...permanentlyDeletePostField,
	isEligible: ( item ) =>
		item.status === 'trash' &&
		select( coreStore ).canUser( 'delete', { kind: 'postType', name: item.type, id: item.id } ),
};

export default permanentlyDeletePost;
