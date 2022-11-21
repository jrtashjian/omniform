/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useEntityProp } from '@wordpress/core-data';
import { TextControl } from '@wordpress/components';
import { InspectorControls } from '@wordpress/block-editor';

/**
 * Internal dependencies
 */
import { FORM_POST_TYPE } from '../../shared/constants';

export default function FormInspectorControls( {
	formId,
	isEntityAvailable,
} ) {
	const [ title, setTitle ] = useEntityProp(
		'postType',
		FORM_POST_TYPE,
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
