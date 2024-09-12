/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
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
							style: {
								layout: {
									selfStretch: 'fill',
									flexSize: null,
								},
							},
							layout: {
								type: 'flex',
								orientation: 'horizontal',
								justifyContent: 'space-between',
							},
						},
						innerBlocks: [
							{
								name: 'omniform/input',
								attributes: {
									fieldType: 'search',
									fieldPlaceholder: '',
									style: {
										layout: {
											selfStretch: 'fill',
											flexSize: null,
										},
									},
								},
							},
						],
					},
					{
						name: 'omniform/button',
						attributes: {
							buttonType: 'submit',
							buttonLabel: 'Search',
							style: {
								layout: {
									selfStretch: 'fit',
									flexSize: null,
								},
							},
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
								justifyContent: 'space-between',
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
							layout: {
								type: 'default',
							},
						},
						innerBlocks: [
							{
								name: 'omniform/conditional-group',
								attributes: {
									callback: '{{is_user_logged_in}}',
									reverseCondition: false,
									layout: {
										type: 'default',
									},
								},
								innerBlocks: [
									{
										name: 'core/paragraph',
										attributes: {
											content: __( 'Logged in as admin. <a href="/wp-admin/profile.php" data-type="link" data-id="/wp-admin/profile.php">Edit your profile</a>. <a href="/wp-login.php?action=logout" data-type="link" data-id="/wp-login.php?action=logout">Log out?</a> Required fields are marked *', 'omniform' ),
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
									layout: {
										type: 'default',
									},
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
									createInputField( __( 'Website', 'omniform' ), 'text', true, { fieldName: 'url', fieldValue: '{{omniform_current_commenter_url}}' } ),
									{
										name: 'omniform/conditional-group',
										attributes: {
											callback: '{{omniform_comment_cookies_opt_in}}',
											reverseCondition: false,
										},
										innerBlocks: [
											createInputField( __( 'Save my name, email, and website in this browser for the next time I comment.', 'omniform' ), 'checkbox', true, { fieldName: 'wp-comment-cookies-consent', fieldValue: '{{omniform_current_commenter_author}}' } ),
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
									content: __( 'You must be <a href="/wp-login.php">logged in</a> to post a comment.', 'omniform' ),
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
