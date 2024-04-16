/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useEntityProp } from '@wordpress/core-data';
import { PanelBody, TextControl, SelectControl } from '@wordpress/components';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';

/**
 * Internal dependencies
 */
import { POST_TYPE } from '../../block-library/shared/constants';

export default function SubmissionMethodSettings( {
	formId,
	isDocumentPanel,
} ) {
	const [ meta, setMeta ] = useEntityProp( 'postType', POST_TYPE, 'meta', formId );

	const metaSubmitMethod = meta?.submit_method;
	const updateMetaSubmitMethod = ( newValue ) => {
		setMeta( { ...meta, submit_method: newValue } );
	};

	const metaSubmitAction = meta?.submit_action;
	const updateMetaSubmitAction = ( newValue ) => {
		setMeta( { ...meta, submit_action: newValue } );
	};

	const PanelComponent = isDocumentPanel
		? PluginDocumentSettingPanel
		: PanelBody;

	return (
		<PanelComponent
			name="omniform-submission-method"
			title={ __( 'Submission', 'omniform' ) }
		>
			<TextControl
				label={ __( 'Submit Method', 'omniform' ) }
				value={ metaSubmitAction }
				onChange={ updateMetaSubmitAction }
			/>
			<SelectControl
				label={ __( 'Submit Action', 'omniform' ) }
				value={ metaSubmitMethod }
				onChange={ updateMetaSubmitMethod }
				options={ [
					{ label: 'POST', value: 'POST' },
					{ label: 'GET', value: 'GET' },
				] }
			/>
		</PanelComponent>
	);
}
