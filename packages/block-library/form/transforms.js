/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { createBlock } from '@wordpress/blocks';

const transforms = {
	from: [
		{
			type: 'block',
			blocks: [ 'core/search' ],
			transform: ( {
				label,
				showLabel,
				buttonText,
				placeholder,
				buttonPosition,
				...attributes
			} ) => {
				const fieldInnerBlocks = [
					createBlock( 'omniform/input', {
						fieldType: 'search',
						fieldPlaceholder: placeholder,
						style: {
							layout: {
								selfStretch: 'fill',
								flexSize: null,
							},
							border: attributes?.style?.border,
						},
						borderColor: attributes?.borderColor,
					}, [] ),
				];

				if ( showLabel && label ) {
					fieldInnerBlocks.unshift( createBlock( 'omniform/label', {}, [] ) );
				}

				const innerBlocks = [
					createBlock( 'omniform/field',
						{
							fieldLabel: label || __( 'Search', 'omniform' ),
							fieldName: 's',
							style: {
								layout: {
									selfStretch: 'fill',
									flexSize: null,
								},
							},
							layout: showLabel ? {
								type: 'flex',
								orientation: 'vertical',
								justifyContent: 'stretch',
							} : {
								type: 'flex',
								orientation: 'horizontal',
								justifyContent: 'space-between',
							},
						},
						fieldInnerBlocks
					),
				];

				if ( buttonPosition !== 'no-button' ) {
					innerBlocks.push(
						createBlock( 'omniform/button',
							{
								buttonType: 'submit',
								buttonLabel: buttonText || __( 'Search', 'omniform' ),
								style: {
									layout: {
										selfStretch: 'fit',
										flexSize: null,
									},
									border: attributes?.style?.border,
									typography: attributes?.style?.typography,
								},
								textColor: attributes?.textColor,
								backgroundColor: attributes?.backgroundColor,
								borderColor: attributes?.borderColor,
							}
						),
					);
				}

				return createBlock( 'omniform/form',
					{
						form_type: 'custom',
						submit_action: '{{get_site_url}}',
						submit_method: 'GET',
						required_label: '*',
					},
					[
						createBlock( 'core/group',
							{
								tagName: 'div',
								style: {
									spacing: {
										margin: {
											top: 0,
											bottom: 0,
										},
									},
									typography: attributes?.style?.typography,
								},
								layout: {
									type: 'flex',
									flexWrap: 'nowrap',
									justifyContent: 'space-between',
									orientation: 'horizontal',
									verticalAlignment: showLabel ? 'bottom' : undefined,
								},
								fontFamily: attributes?.fontFamily,
								fontSize: attributes?.fontSize,
							},
							innerBlocks
						),
					]
				);
			},
		},
		{
			type: 'block',
			blocks: [ 'core/post-comments-form' ],
			transform: ( {
				...attributes
			} ) => {
				return createBlock( 'omniform/form',
					{
						form_type: 'custom',
						submit_action: '{{get_site_url}}/wp-comments-post.php',
						submit_method: 'POST',
						required_label: '*',
					},
					[
						createBlock( 'core/group',
							{
								tagName: 'div',
								align: 'full',
								layout: {
									type: 'default',
								},
							},
							[
								createBlock( 'core/group',
									{
										tagName: 'div',
										layout: {
											type: 'flex',
											flexWrap: 'nowrap',
											verticalAlignment: 'center',
											justifyContent: 'left',
										},
									},
									[
										createBlock( 'omniform/post-comments-form-title',
											{
												noReplyText: 'Leave a Reply',
												replyText: 'Leave a Reply to %s',
												linkToParent: true,
												level: 3,
											},
											[]
										),
										createBlock( 'omniform/post-comments-form-cancel-reply-link',
											{
												linkText: 'Cancel reply',
											},
											[]
										),
									]
								),
								createBlock( 'omniform/conditional-group',
									{
										callback: '{{omniform_open_for_comments}}',
										reverseCondition: false,
										layout: {
											type: 'default',
										},
									},
									[
										createBlock( 'omniform/conditional-group',
											{
												callback: '{{is_user_logged_in}}',
												reverseCondition: false,
												layout: {
													type: 'default',
												},
											},
											[
												createBlock( 'core/paragraph', {
													content: 'Logged in as {{omniform_current_user_display_name}}. <a href="{{get_edit_user_link}}">Edit your profile</a>. <a href="{{omniform_comment_logout_url}">Log out?</a> Required fields are marked *',
												} ),
											]
										),
										createBlock( 'omniform/conditional-group',
											{
												callback: '{{is_user_logged_in}}',
												reverseCondition: true,
												layout: {
													type: 'default',
												},
											},
											[
												createBlock( 'core/paragraph', {
													content: 'Your email address will not be published. Required fields are marked *',
												} ),
											]
										),
										createBlock( 'omniform/field',
											{
												fieldLabel: 'Comment',
												fieldName: 'comment',
												isRequired: true,
											},
											[
												createBlock( 'omniform/label' ),
												createBlock( 'omniform/textarea', {
													fieldValue: '',
													style: {
														dimensions: {
															minHeight: '230px',
														},
													},
												} ),
											]
										),
										createBlock( 'omniform/conditional-group',
											{
												callback: '{{is_user_logged_in}}',
												reverseCondition: true,
											},
											[
												createBlock( 'omniform/field',
													{
														fieldLabel: 'Name',
														fieldName: 'author',
														isRequired: true,
													},
													[
														createBlock( 'omniform/label' ),
														createBlock( 'omniform/input', {
															fieldType: 'text',
															fieldValue: '{{omniform_current_commenter_author}}',
														} ),
													]
												),
												createBlock( 'omniform/field',
													{
														fieldLabel: 'Email',
														fieldName: 'email',
														isRequired: true,
													},
													[
														createBlock( 'omniform/label' ),
														createBlock( 'omniform/input', {
															fieldType: 'text',
															fieldValue: '{{omniform_current_commenter_email}}',
														} ),
													]
												),
												createBlock( 'omniform/field',
													{
														fieldLabel: 'Website',
														fieldName: 'url',
														isRequired: false,
													},
													[
														createBlock( 'omniform/label' ),
														createBlock( 'omniform/input', {
															fieldType: 'text',
															fieldValue: '{{omniform_current_commenter_url}}',
														} ),
													]
												),
												createBlock( 'omniform/conditional-group',
													{
														callback: '{{omniform_comment_cookies_opt_in}}',
														reverseCondition: false,
													},
													[
														createBlock( 'omniform/field',
															{
																fieldLabel: 'Save my name, email, and website in this browser for the next time I comment.',
																fieldName: 'wp-comment-cookies-consent',
																isRequired: false,
																layout: {
																	type: 'flex',
																	orientation: 'horizontal',
																	justifyContent: 'left',
																	flexWrap: 'nowrap',
																	verticalAlignment: 'center',
																},
															},
															[
																createBlock( 'omniform/input', {
																	fieldType: 'checkbox',
																	fieldValue: '{{omniform_current_commenter_author}}',
																} ),
																createBlock( 'omniform/label' ),
															]
														),
													]
												),
											]
										),
										createBlock( 'core/group',
											{
												tagName: 'div',
												layout: {
													type: 'flex',
													flexWrap: 'nowrap',
													justifyContent: 'left',
													orientation: 'horizontal',
												},
											},
											[
												createBlock( 'omniform/button', {
													buttonType: 'submit',
													buttonLabel: 'Post Comment',
												} ),
											],
										),
									]
								),
								createBlock( 'omniform/conditional-group',
									{
										callback: '{{omniform_closed_for_comments}}',
										reverseCondition: false,
									},
									[
										createBlock( 'core/paragraph', {
											content: 'Comments are closed.',
										} ),
									]
								),
								createBlock( 'omniform/conditional-group',
									{
										callback: '{{omniform_comment_login_required}}',
										reverseCondition: false,
									},
									[
										createBlock( 'core/paragraph', {
											content: 'You must be <a href="{{omniform_comment_login_url}}">logged in</a> to post a comment.',
										} ),
									]
								),
							],
						),
					],
				);
			},
		},
	],
};

export default transforms;
