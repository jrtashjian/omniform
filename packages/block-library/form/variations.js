/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

const variations = [
	{
		name: 'search-form',
		title: __( 'Search Form', 'omniform' ),
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
];

export default variations;
