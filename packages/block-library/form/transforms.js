/**
 * WordPress dependencies
 */
import { __, sprintf } from '@wordpress/i18n';
import { createBlock } from '@wordpress/blocks';

const transforms = {
	from: [
		{
			type: 'block',
			blocks: [ 'core/group' ],
			isMatch: ( attributes ) => attributes?.metadata?.name === 'Sample Form',
			transform: ( attributes, innerBlocks ) => {
				const transformBlocks = ( blocks ) => {
					return blocks.reduce( ( transformedBlocks, block ) => {
						// Recursively transform inner blocks.
						if ( block.innerBlocks && block.innerBlocks.length > 0 ) {
							block.innerBlocks = transformBlocks( block.innerBlocks );
						}

						// Convert "Sample Field" core/group blocks to omniform/field blocks.
						if (
							block.name === 'core/group' &&
							block.attributes?.metadata?.name === 'Sample Field' &&
							block.innerBlocks?.[ 0 ].name === 'core/paragraph'
						) {
							// The inner paragraph should be the fieldPlaceholder and fieldLabel fallback.
							const fieldPlaceholder = block.innerBlocks?.[ 0 ]?.attributes?.content?.text.trim().replace( /\*$/, '' );
							let fieldLabel = fieldPlaceholder;
							let isRequired = block.innerBlocks?.[ 0 ]?.attributes?.content?.text.trim().endsWith( '*' );

							// Default to placeholder labels.
							let hasLabel = false;

							const controlType = block.attributes?.style?.dimensions?.minHeight
								? 'omniform/textarea'
								: 'omniform/input';

							const prevBlock = transformedBlocks[ transformedBlocks.length - 1 ];
							if ( prevBlock && prevBlock.name === 'core/paragraph' ) {
								hasLabel = true;
								fieldLabel = prevBlock.attributes.content.originalHTML.trim().replace( '*', '' );
								isRequired = prevBlock.attributes.content.text.trim().endsWith( '*' );
								// Remove the previous block if it was used as the fieldLabel.
								transformedBlocks.pop();
							}

							const fieldInnerBlocks = [
								createBlock( controlType, {
									fieldPlaceholder,
									backgroundColor: block.attributes?.backgroundColor,
									textColor: block.attributes?.textColor,
									style: {
										...block.attributes?.style || {},
										dimensions: { minHeight: block.attributes?.style?.dimensions?.minHeight },
									},
								} ),
							];

							if ( hasLabel ) {
								fieldInnerBlocks.unshift( createBlock( 'omniform/label' ) );
							}

							transformedBlocks.push(
								createBlock( 'omniform/field', {
									fieldLabel,
									isRequired,
									style: {
										spacing: { blockGap: attributes?.style?.spacing?.blockGap },
									},
								}, fieldInnerBlocks )
							);

							return transformedBlocks;
						}

						// Convert core/buttons blocks to omniform/button blocks.
						if ( block.name === 'core/buttons' ) {
							transformedBlocks.push(
								createBlock( 'core/group', {
									layout: {
										type: 'flex',
										orientation: 'horizontal',
									},
									style: block.attributes?.style,
								}, [
									createBlock( 'omniform/button', {
										buttonLabel: block.innerBlocks[ 0 ].attributes.text.text,
										buttonType: 'submit',
										backgroundColor: block.innerBlocks[ 0 ].attributes?.backgroundColor,
										textColor: block.innerBlocks[ 0 ].attributes?.textColor,
										style: {
											...block.innerBlocks[ 0 ].attributes?.style || {},
											layout: {
												selfStretch: block.innerBlocks[ 0 ].attributes?.width ? 'fill' : undefined,
											},
										},
									} ),
								] )
							);

							return transformedBlocks;
						}

						// Convert core/paragraph blocks with the "replace me" note.
						if (
							block.name === 'core/paragraph' &&
							block.attributes.content.text === 'This is a sample form that you should replace with your own form solution.'
						) {
							return transformedBlocks;
						}

						transformedBlocks.push( block );
						return transformedBlocks;
					}, [] );
				};

				return createBlock(
					'omniform/form', {}, [
						createBlock( 'core/group', {
							style: attributes?.style,
						}, transformBlocks( innerBlocks ) ),
					]
				);
			},
		},
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
			transform: ( attributes ) => {
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
								style: attributes?.style,
								fontSize: attributes?.fontSize,
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
												noReplyText: __( 'Leave a Reply', 'omniform' ),
												/* translators: %s: author name */
												replyText: __( 'Leave a Reply to %s', 'omniform' ),
												linkToParent: true,
												level: 3,
											},
											[]
										),
										createBlock( 'omniform/post-comments-form-cancel-reply-link',
											{
												linkText: __( 'Cancel reply', 'omniform' ),
											},
											[]
										),
									]
								),
								createBlock( 'omniform/conditional-group',
									{
										callback: '{{omniform_open_for_comments}}',
										reverseCondition: false,
									},
									[
										createBlock( 'omniform/conditional-group',
											{
												callback: '{{is_user_logged_in}}',
												reverseCondition: false,
											},
											[
												createBlock( 'core/paragraph', {
													content: sprintf(
														/* translators: 1: User name, 2: Edit user link, 3: Logout URL. */
														__( 'Logged in as %1$s. <a href="%2$s">Edit your profile</a>. <a href="%3$s">Log out?</a> Required fields are marked *', 'omniform' ),
														'{{omniform_current_user_display_name}}',
														'{{get_edit_user_link}}',
														'{{omniform_comment_logout_url}'
													),
												} ),
											]
										),
										createBlock( 'omniform/conditional-group',
											{
												callback: '{{is_user_logged_in}}',
												reverseCondition: true,
											},
											[
												createBlock( 'core/paragraph', {
													content: __( 'Your email address will not be published. Required fields are marked *', 'omniform' ),
												} ),
											]
										),
										createBlock( 'omniform/field',
											{
												fieldLabel: __( 'Comment', 'omniform' ),
												fieldName: 'comment',
												isRequired: true,
											},
											[
												createBlock( 'omniform/label' ),
												createBlock( 'omniform/textarea', {
													fieldValue: '',
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
														fieldLabel: __( 'Name', 'omniform' ),
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
														fieldLabel: __( 'Email', 'omniform' ),
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
														fieldLabel: __( 'Website', 'omniform' ),
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
																className: 'is-style-inline',
																fieldLabel: __( 'Save my name, email, and website in this browser for the next time I comment.', 'omniform' ),
																fieldName: 'wp-comment-cookies-consent',
																isRequired: false,
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
													buttonLabel: __( 'Post Comment', 'omniform' ),
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
											content: __( 'Comments are closed.', 'omniform' ),
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
											content: sprintf(
												/* translators: %s: Login URL. */
												__( 'You must be <a href="%s">logged in</a> to post a comment.', 'omniform' ),
												'{{omniform_comment_login_url}}'
											),
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
