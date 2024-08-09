/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { PanelBody, TextControl, SelectControl } from '@wordpress/components';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { useSelect } from '@wordpress/data';
import { store as editorStore } from '@wordpress/editor';

export default function SubmissionMethodSettings( {
	isDocumentPanel,
	getSetting,
	setSetting,
} ) {
	const formTypes = useSelect( ( select ) => {
		const editorSettings = select( editorStore ).getEditorSettings();
		return editorSettings.omniformFormTypes ?? [];
	}, [] );

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
				value={ getSetting( 'form_type' ) || 'standard' }
				onChange={ ( newValue ) => setSetting( 'form_type', newValue ) }
				options={ formTypes.map( ( item ) => ( {
					label: item.label,
					value: item.type,
				} ) ) }
				help={ __( 'Determines how the form data is processed and submitted.', 'omniform' ) }
			/>

			{ getSetting( 'form_type' ) === 'custom' && (
				<>
					<TextControl
						label={ __( 'Submit Action', 'omniform' ) }
						value={ getSetting( 'submit_action' ) || '' }
						onChange={ ( newValue ) => setSetting( 'submit_action', newValue ) }
						help={ __( 'Enter the URL where the form data will be submitted.', 'omniform' ) }
					/>
					<SelectControl
						label={ __( 'Submit Method', 'omniform' ) }
						value={ getSetting( 'submit_method' ) || 'POST' }
						onChange={ ( newValue ) => setSetting( 'submit_method', newValue ) }
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
