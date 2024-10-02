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

				if ( showLabel ) {
					fieldInnerBlocks.unshift( createBlock( 'omniform/label', {}, [] ) );
				}

				const innerBlocks = [
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
				];

				if ( buttonPosition !== 'no-button' ) {
					innerBlocks.push(
						createBlock( 'omniform/button',
							{
								buttonType: 'submit',
								buttonLabel: buttonText,
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
	],
};

export default transforms;
