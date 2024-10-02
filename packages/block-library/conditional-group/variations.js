/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

const variations = [
	{
		name: 'conditional-comment-login-required',
		title: __( 'Commenter must login', 'omniform' ),
		description: __( 'Check if the current user is required to login to comment.', 'omniform' ),
		attributes: { callback: '{{omniform_comment_login_required}}' },
	},
	{
		name: 'conditional-closed-for-comments',
		title: __( 'Closed for comments', 'omniform' ),
		description: __( 'Check if comments are closed for the current post.', 'omniform' ),
		attributes: { callback: '{{omniform_closed_for_comments}}' },
	},
	{
		name: 'conditional-open-for-comments',
		title: __( 'Open for comments', 'omniform' ),
		description: __( 'Check if comments are open for the current post.', 'omniform' ),
		attributes: { callback: '{{omniform_open_for_comments}}' },
	},
	{
		name: 'conditional-comment-cookies-opt-in',
		title: __( 'Cookie consent enabled', 'omniform' ),
		description: __( 'Check if the comment cookies opt-in is enabled.', 'omniform' ),
		attributes: { callback: '{{omniform_comment_cookies_opt_in}}' },
	},
	{
		name: 'conditional-is-user-logged-in',
		title: __( 'Logged in', 'omniform' ),
		description: __( 'Check if the current visitor is logged in.', 'omniform' ),
		attributes: { callback: '{{is_user_logged_in}}' },
	},
];

variations.forEach( ( variation ) => {
	variation.isActive = ( blockAttributes, variationAttributes ) =>
		blockAttributes.callback ===
		variationAttributes.callback;

	if ( ! variation.scope ) {
		variation.scope = [ 'inserter', 'transform' ];
	}
} );

export default variations;
