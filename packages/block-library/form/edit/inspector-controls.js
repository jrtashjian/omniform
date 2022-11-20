/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useEntityProp } from '@wordpress/core-data';
import { TextControl } from '@wordpress/components';
import { InspectorControls } from '@wordpress/block-editor';

export default function FormInspectorControls( {
	formId,
	isEntityAvailable,
} ) {
	const [ title, setTitle ] = useEntityProp(
		'postType',
		'inquirywp_form',
		'title',
		formId
	);

	return (
		<InspectorControls __experimentalGroup="advanced">
			{ isEntityAvailable && (
				<TextControl
					label={ __( 'Title', 'inquirywp' ) }
					value={ title }
					onChange={ setTitle }
				/>
			) }
		</InspectorControls>
	);
}
