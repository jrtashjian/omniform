/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useEntityProp } from '@wordpress/core-data';
import { PanelBody, TextControl, SelectControl } from '@wordpress/components';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { useSelect } from '@wordpress/data';
import { store as editorStore } from '@wordpress/editor';

/**
 * Internal dependencies
 */
import { POST_TYPE } from '../../block-library/shared/constants';

export default function SubmissionMethodSettings( {
	formId,
	isDocumentPanel,
} ) {
	const formTypes = useSelect( ( select ) => {
		const editorSettings = select( editorStore ).getEditorSettings();
		return editorSettings.omniformFormTypes ?? [];
	}, [] );

	const [ formType, setFormType ] = useEntityProp( 'postType', POST_TYPE, 'omniform_type', formId );
	const [ meta, setMeta ] = useEntityProp( 'postType', POST_TYPE, 'meta', formId );

	const metaSubmitMethod = meta?.submit_method;
	const updateMetaSubmitMethod = ( newValue ) => {
		setMeta( { ...meta, submit_method: newValue } );
	};

	const metaSubmitAction = meta?.submit_action;
	const updateMetaSubmitAction = ( newValue ) => {
		setMeta( { ...meta, submit_action: newValue } );
	};

	const updateFormType = ( newValue ) => {
		setFormType( newValue );

		// Reset the meta values when switching to standard form type.
		if ( newValue === 'uncategorized' ) {
			setMeta( {
				...meta,
				submit_method: '',
				submit_action: '',
			} );
		}
	};

	const PanelComponent = isDocumentPanel
		? PluginDocumentSettingPanel
		: PanelBody;

	return (
		<PanelComponent
			name="omniform-submission-method"
			title={ __( 'Submission', 'omniform' ) }
		>
			<SelectControl
				label={ __( 'Type', 'omniform' ) }
				value={ formType.toString() || 'uncategorized' }
				onChange={ updateFormType }
				options={ formTypes.map( ( item ) => ( {
					label: item.label,
					value: item.type,
				} ) ) }
				help={ __( 'Determines how the form data is processed and submitted.', 'omniform' ) }
			/>

			{ formType === 'custom' && (
				<>
					<TextControl
						label={ __( 'Submit Method', 'omniform' ) }
						value={ metaSubmitAction }
						onChange={ updateMetaSubmitAction }
						help={ __( 'Enter the URL where the form data will be submitted.', 'omniform' ) }
					/>
					<SelectControl
						label={ __( 'Submit Action', 'omniform' ) }
						value={ metaSubmitMethod }
						onChange={ updateMetaSubmitMethod }
						options={ [
							{ label: 'POST', value: 'POST' },
							{ label: 'GET', value: 'GET' },
						] }
						help={ __( 'Select the HTTP method to use for submission.', 'omniform' ) }
					/>
				</>
			) }
		</PanelComponent>
	);
}
