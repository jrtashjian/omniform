/**
 * WordPress dependencies
 */
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
			} ) => {
				const fieldInnerBlocks = [
					createBlock( 'omniform/input', {
						fieldType: 'search',
						fieldPlaceholder: '',
						style: {
							layout: {
								selfStretch: 'fill',
								flexSize: null,
							},
						},
					}, [] ),
				];

				if ( showLabel ) {
					fieldInnerBlocks.unshift( createBlock( 'omniform/label', {}, [] ) );
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
								},
								layout: {
									type: 'flex',
									flexWrap: 'nowrap',
									justifyContent: 'space-between',
									orientation: 'horizontal',
									verticalAlignment: showLabel ? 'bottom' : undefined,
								},
							},
							[
								createBlock( 'omniform/field',
									{
										fieldLabel: label,
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
								createBlock( 'omniform/button',
									{
										buttonType: 'submit',
										buttonLabel: buttonText,
										style: {
											layout: {
												selfStretch: 'fit',
												flexSize: null,
											},
										},
									}
								),
							]
						),
					]
				);
			},
		},
	],
};

export default transforms;
