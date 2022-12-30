/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	__experimentalToggleGroupControl as ToggleGroupControl,
	__experimentalToggleGroupControlOption as ToggleGroupControlOption,
} from '@wordpress/components';

export default function FieldInspectorControls( {
	originBlockProps,
	showRequiredControl,
	showLabelControl,
} ) {
	const {
		attributes,
		setAttributes,
	} = originBlockProps;

	const onRequiredChange = ( enable ) => setAttributes( { isRequired: enable } );
	const onLabelHiddenChange = ( enable ) => setAttributes( {
		isLabelHidden: enable,
		fieldPlaceholder: enable ? attributes.fieldLabel : '',
	} );

	return (
		<InspectorControls>
			<PanelBody title={ __( 'Field Settings', 'omniform' ) }>

				{ showRequiredControl && (
					<ToggleGroupControl
						label={ __( 'Required field?', 'omniform' ) }
						value={ !! attributes.isRequired }
						onChange={ onRequiredChange }
						isBlock
					>
						<ToggleGroupControlOption
							value={ false }
							label={ __( 'Optional', 'omniform' ) }
						/>
						<ToggleGroupControlOption
							value={ true }
							label={ __( 'Required', 'omniform' ) }
						/>
					</ToggleGroupControl>
				) }

				{ showLabelControl && (
					<ToggleGroupControl
						label={ __( 'Hide the label?', 'omniform' ) }
						value={ !! attributes.isLabelHidden }
						onChange={ onLabelHiddenChange }
						isBlock
					>
						<ToggleGroupControlOption
							value={ false }
							label={ __( 'Shown', 'omniform' ) }
						/>
						<ToggleGroupControlOption
							value={ true }
							label={ __( 'Hidden', 'omniform' ) }
						/>
					</ToggleGroupControl>
				) }

			</PanelBody>
		</InspectorControls>
	);
}
