/**
 * WordPress dependencies
 */
import { __, sprintf } from '@wordpress/i18n';
import { createInputField, createTextareaField } from '../shared/variations';

const variations = [
	{
		name: 'search-form',
		title: __( 'Search', 'omniform' ),
		description: __( 'A form to search for content.', 'omniform' ),
		attributes: {
			form_type: 'custom',
			submit_method: 'GET',
			submit_action: '{{get_site_url}}',
		},
		innerBlocks: [
			{
				name: 'core/group',
				attributes: {
					style: {
						spacing: {
							margin: {
								top: '0',
								bottom: '0',
							},
						},
					},
					layout: {
						type: 'flex',
						flexWrap: 'nowrap',
						justifyContent: 'space-between',
						orientation: 'horizontal',
					},
				},
				innerBlocks: [
					{
						name: 'omniform/field',
						attributes: {
							fieldLabel: __( 'Search', 'omniform' ),
							fieldName: 's',
							isRequired: true,
						},
						innerBlocks: [
							{
								name: 'omniform/input',
								attributes: {
									fieldType: 'search',
									fieldPlaceholder: '',
								},
							},
						],
					},
					{
						name: 'omniform/button',
						attributes: {
							buttonType: 'submit',
							buttonLabel: 'Search',
						},
					},
				],
			},
		],
	},
	{
		name: 'comments-form',
		title: __( 'Comments Form', 'omniform' ),
		description: __( 'A form to submit comments.', 'omniform' ),
		attributes: {
			form_type: 'custom',
			submit_method: 'POST',
			submit_action: '{{get_site_url}}/wp-comments-post.php',
		},
		innerBlocks: [
			{
				name: 'core/group',
				attributes: {
					tagName: 'div',
					align: 'full',
					layout: {
						type: 'default',
					},
				},
				innerBlocks: [
					{
						name: 'core/group',
						attributes: {
							tagName: 'div',
							layout: {
								type: 'flex',
								flexWrap: 'nowrap',
								verticalAlignment: 'center',
								justifyContent: 'left',
							},
						},
						innerBlocks: [
							{
								name: 'omniform/post-comments-form-title',
								attributes: {
									noReplyText: __( 'Leave a Reply', 'omniform' ),
									/* translators: %s: author name */
									replyText: __( 'Leave a Reply to %s', 'omniform' ),
									linkToParent: true,
									level: 3,
								},
								innerBlocks: [],
							},
							{
								name: 'omniform/post-comments-form-cancel-reply-link',
								attributes: {
									linkText: __( 'Cancel reply', 'omniform' ),
								},
								innerBlocks: [],
							},
						],
					},
					{
						name: 'omniform/conditional-group',
						attributes: {
							callback: '{{omniform_open_for_comments}}',
							reverseCondition: false,
						},
						innerBlocks: [
							{
								name: 'omniform/conditional-group',
								attributes: {
									callback: '{{is_user_logged_in}}',
									reverseCondition: false,
								},
								innerBlocks: [
									{
										name: 'core/paragraph',
										attributes: {
											content: sprintf(
												/* translators: 1: User name, 2: Edit user link, 3: Logout URL. */
												__( 'Logged in as %1$s. <a href="%2$s">Edit your profile</a>. <a href="%3$s">Log out?</a> Required fields are marked *', 'omniform' ),
												'{{omniform_current_user_display_name}}',
												'{{get_edit_user_link}}',
												'{{omniform_comment_logout_url}'
											),
											dropCap: false,
										},
										innerBlocks: [],
									},
								],
							},
							{
								name: 'omniform/conditional-group',
								attributes: {
									callback: '{{is_user_logged_in}}',
									reverseCondition: true,
								},
								innerBlocks: [
									{
										name: 'core/paragraph',
										attributes: {
											content: __( 'Your email address will not be published. Required fields are marked *', 'omniform' ),
											dropCap: false,
										},
										innerBlocks: [],
									},
								],
							},
							createTextareaField( __( 'Comment', 'omniform' ), true ),
							{
								name: 'omniform/conditional-group',
								attributes: {
									callback: '{{is_user_logged_in}}',
									reverseCondition: true,
								},
								innerBlocks: [
									createInputField( __( 'Name', 'omniform' ), 'text', true, { fieldName: 'author', fieldValue: '{{omniform_current_commenter_author}}' } ),
									createInputField( __( 'Email', 'omniform' ), 'text', true, { fieldName: 'email', fieldValue: '{{omniform_current_commenter_email}}' } ),
									createInputField( __( 'Website', 'omniform' ), 'text', false, { fieldName: 'url', fieldValue: '{{omniform_current_commenter_url}}' } ),
									{
										name: 'omniform/conditional-group',
										attributes: {
											callback: '{{omniform_comment_cookies_opt_in}}',
											reverseCondition: false,
										},
										innerBlocks: [
											createInputField( __( 'Save my name, email, and website in this browser for the next time I comment.', 'omniform' ), 'checkbox', false, { fieldName: 'wp-comment-cookies-consent', fieldValue: '{{omniform_current_commenter_author}}' } ),
										],
									},
								],
							},
							{
								name: 'core/group',
								attributes: {
									tagName: 'div',
									layout: {
										type: 'flex',
										flexWrap: 'nowrap',
										justifyContent: 'left',
										orientation: 'horizontal',
									},
								},
								innerBlocks: [
									{
										name: 'omniform/button',
										attributes: {
											buttonType: 'submit',
											buttonLabel: __( 'Post Comment', 'omniform' ),
										},
										innerBlocks: [],
									},
								],
							},
						],
					},
					{
						name: 'omniform/conditional-group',
						attributes: {
							callback: '{{omniform_closed_for_comments}}',
							reverseCondition: false,
						},
						innerBlocks: [
							{
								name: 'core/paragraph',
								attributes: {
									content: __( 'Comments are closed.', 'omniform' ),
									dropCap: false,
								},
								innerBlocks: [],
							},
						],
					},
					{
						name: 'omniform/conditional-group',
						attributes: {
							callback: '{{omniform_comment_login_required}}',
							reverseCondition: false,
						},
						innerBlocks: [
							{
								name: 'core/paragraph',
								attributes: {
									content: sprintf(
										/* translators: %s: Login URL. */
										__( 'You must be <a href="%s">logged in</a> to post a comment.', 'omniform' ),
										'{{omniform_comment_login_url}}'
									),
									dropCap: false,
								},
								innerBlocks: [],
							},
						],
					},
				],
			},
		],
	},
];

export default variations;
