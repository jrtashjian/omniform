/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

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
						name: 'core/heading',
						attributes: {
							content: 'Leave a Reply',
							level: 3,
							anchor: 'reply-title',
						},
						innerBlocks: [],
					},
					{
						name: 'core/paragraph',
						attributes: {
							content: 'Your email address will not be published. Required fields are marked *',
							dropCap: false,
						},
						innerBlocks: [],
					},
					{
						name: 'omniform/field',
						attributes: {
							fieldLabel: 'Comment',
							fieldName: 'comment',
							isRequired: true,
						},
						innerBlocks: [
							{
								name: 'omniform/label',
								attributes: {},
								innerBlocks: [],
							},
							{
								name: 'omniform/textarea',
								attributes: {
									className: 'comment-form-comment',
									style: {
										dimensions: {
											minHeight: '230px',
										},
									},
								},
								innerBlocks: [],
							},
						],
					},
					{
						name: 'omniform/field',
						attributes: {
							fieldLabel: 'Name',
							fieldName: 'author',
							isRequired: true,
						},
						innerBlocks: [
							{
								name: 'omniform/label',
								attributes: {},
								innerBlocks: [],
							},
							{
								name: 'omniform/input',
								attributes: {
									fieldType: 'text',
									fieldPlaceholder: '',
									fieldValue: '{{omniform_current_commenter_author}}',
								},
								innerBlocks: [],
							},
						],
					},
					{
						name: 'omniform/field',
						attributes: {
							fieldLabel: 'Email',
							fieldName: 'email',
							isRequired: true,
						},
						innerBlocks: [
							{
								name: 'omniform/label',
								attributes: {},
								innerBlocks: [],
							},
							{
								name: 'omniform/input',
								attributes: {
									fieldType: 'email',
									fieldValue: '{{omniform_current_commenter_email}}',
								},
								innerBlocks: [],
							},
						],
					},
					{
						name: 'omniform/field',
						attributes: {
							fieldLabel: 'Website',
							fieldName: 'url',
						},
						innerBlocks: [
							{
								name: 'omniform/label',
								attributes: {},
								innerBlocks: [],
							},
							{
								name: 'omniform/input',
								attributes: {
									fieldType: 'url',
									fieldPlaceholder: '',
									fieldValue: '{{omniform_current_commenter_url}}',
								},
								innerBlocks: [],
							},
						],
					},
					{
						name: 'omniform/field',
						attributes: {
							fieldLabel: 'Save my name, email, and website in this browser for the next time I comment.',
							fieldName: 'wp-comment-cookies-consent',
							layout: {
								type: 'flex',
								orientation: 'horizontal',
								justifyContent: 'left',
								flexWrap: 'nowrap',
							},
						},
						innerBlocks: [
							{
								name: 'omniform/input',
								attributes: {
									fieldType: 'checkbox',
								},
								innerBlocks: [],
							},
							{
								name: 'omniform/label',
								attributes: {},
								innerBlocks: [],
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
							},
						},
						innerBlocks: [
							{
								name: 'omniform/button',
								attributes: {
									buttonType: 'submit',
									buttonLabel: 'Post Comment',
								},
								innerBlocks: [],
							},
						],
					},
					{
						name: 'omniform/hidden',
						attributes: {
							fieldName: 'comment_post_ID',
							fieldValue: '{{get_the_ID}}',
						},
						innerBlocks: [],
					},
					{
						name: 'omniform/hidden',
						attributes: {
							fieldName: 'comment_parent',
							fieldValue: '0',
						},
						innerBlocks: [],
					},
				],
			},
		],
	},
];

export default variations;
