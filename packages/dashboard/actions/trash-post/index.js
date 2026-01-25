/**
 * WordPress dependencies.
 */
import { trashPost as trashPostField } from '@wordpress/fields';
import { store as coreStore } from '@wordpress/core-data';
import { select } from '@wordpress/data';

const trashPost = {
	...trashPostField,
	isEligible: ( item ) =>
		!! item.status &&
		! [ 'auto-draft', 'trash' ].includes( item.status ) &&
		select( coreStore ).canUser( 'delete', { kind: 'postType', name: item.type, id: item.id } ),
};

export default trashPost;
