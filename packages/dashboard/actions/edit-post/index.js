/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { addQueryArgs } from '@wordpress/url';

const editPost = {
	id: 'edit',
	label: __( 'Edit', 'omniform' ),
	callback: ( items ) => document.location.href = addQueryArgs( 'post.php', { post: items[ 0 ].id, action: 'edit' } ),
	isPrimary: true,
	isEligible: ( item ) => item.status !== 'trash',
};

export default editPost;
