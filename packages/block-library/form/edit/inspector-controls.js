/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	Button,
	Flex,
	Modal,
	PanelBody,
	__experimentalText as Text,
	TextControl,
	__experimentalVStack as VStack,
} from '@wordpress/components';
import {
	InspectorControls,
	store as blockEditorStore,
} from '@wordpress/block-editor';
import { addQueryArgs } from '@wordpress/url';
import { useDispatch, useSelect } from '@wordpress/data';
import { store as noticesStore } from '@wordpress/notices';
import { useState } from '@wordpress/element';

/**
 * Internal dependencies
 */
import EmailNotificationSettings from '../../../components/form-settings/email-notifications';
import ViewResponses from '../../../components/form-settings/view-responses';
import SubmissionMethodSettings from '../../../components/form-settings/submission-method';
import { useCreateFormFromBlocks, useStandaloneFormSettings, useStandardFormSettings } from '../../../block-library/form/utils/hooks';

export default function FormInspectorControls( {
	formId,
	blockObject,
} ) {
	return !! formId
		? <StandardFormInspectorControls formId={ formId } />
		: <StandaloneFormInspectorControls blockObject={ blockObject } />;
}

function StandardFormInspectorControls( { formId } ) {
	const {
		getSetting,
		setSetting,
	} = useStandardFormSettings( formId );

	return (
		<InspectorControls>
			<ViewResponses formId={ formId } />
			<PanelBody title={ __( 'Form Settings', 'omniform' ) }>
				<TextControl
					label={ __( 'Name', 'omniform' ) }
					value={ getSetting( 'form_title' ) || '' }
					onChange={ ( newValue ) => setSetting( 'form_title', newValue ) }
					help={ __( 'This name will not be visible to viewers and is only for identifying the form.', 'omniform' ) }
					__nextHasNoMarginBottom
					__next40pxDefaultSize
				/>
				<Button
					variant="primary"
					href={ addQueryArgs( 'post.php', {
						action: 'edit',
						post: formId,
					} ) }
				>
					{ __( 'View in Form Editor', 'omniform' ) }
				</Button>
			</PanelBody>
			<EmailNotificationSettings
				getSetting={ getSetting }
				setSetting={ setSetting }
			/>
			<SubmissionMethodSettings
				getSetting={ getSetting }
				setSetting={ setSetting }
			/>
		</InspectorControls>
	);
}

function StandaloneFormInspectorControls( { blockObject } ) {
	const {
		getSetting,
		setSetting,
	} = useStandaloneFormSettings( blockObject );

	const innerBlocks = useSelect(
		( select ) => {
			const { getBlocks } = select( blockEditorStore );
			return getBlocks( blockObject.clientId );
		},
		[ blockObject.clientId ]
	);

	const { createSuccessNotice, createErrorNotice } = useDispatch( noticesStore );
	const createFromBlocks = useCreateFormFromBlocks( blockObject.setAttributes, blockObject.attributes );

	const [ modalOpen, setModalOpen ] = useState( false );

	const createForm = async () => {
		try {
			await createFromBlocks(
				innerBlocks,
				getSetting( 'form_title' ),
				getSetting( 'form_type' ),
				{
					submit_action: getSetting( 'submit_action' ),
					submit_method: getSetting( 'submit_method' ),
					notify_email: getSetting( 'notify_email' ),
					notify_email_subject: getSetting( 'notify_email_subject' ),
				}
			);

			// Reset all but the 'ref' block attribute.
			blockObject.setAttributes(
				Object.keys( blockObject.attributes ).reduce( ( acc, key ) => {
					if ( key !== 'ref' ) {
						acc[ key ] = undefined;
					}
					return acc;
				}, {} )
			);

			createSuccessNotice( __( 'Form created', 'omniform' ), { type: 'snackbar' } );
		} catch ( error ) {
			const errorMessage =
				error.message && error.code !== 'unknown_error'
					? error.message
					: __( 'An error occurred while creating the form.', 'omniform' );

			createErrorNotice( errorMessage, { type: 'snackbar' } );
		}
	};

	const handleConvertForm = async () => {
		await createForm();
		setModalOpen( false );
	};

	return (
		<InspectorControls>
			<PanelBody title={ __( 'Form Settings', 'omniform' ) }>
				<TextControl
					label={ __( 'Name', 'omniform' ) }
					value={ getSetting( 'form_title' ) || '' }
					onChange={ ( newValue ) => setSetting( 'form_title', newValue ) }
					help={ __( 'This name will not be visible to viewers and is only for identifying the form.', 'omniform' ) }
					__nextHasNoMarginBottom
					__next40pxDefaultSize
				/>
				<Text>
					{ __( 'Want deeper insights into your form\'s performance?', 'omniform' ) }{ ' ' }
					<Button variant="link" onClick={ () => setModalOpen( true ) }>
						{ __( 'Learn more', 'omniform' ) }
					</Button>
				</Text>
				{ modalOpen && (
					<Modal
						title={ __( 'Convert to Standard Form', 'omniform' ) }
						onRequestClose={ () => setModalOpen( false ) }
						size="medium"
					>
						<VStack spacing={ 8 }>
							<VStack spacing={ 4 }>
								<Text>
									{ __( 'Your current form supports direct submissions and email notifications, but converting unlocks advanced features to help you get more from your form.', 'omniform' ) }
								</Text>
								<Text>
									{ __( 'Convert to access:', 'omniform' ) }
								</Text>
								<Flex>
									<Text>
										<strong>{ __( 'Analytics', 'omniform' ) }</strong>{ ' ' }
										{ __( 'for tracking submissions and engagement', 'omniform' ) }
									</Text>
									<Text>
										<strong>{ __( 'Response tracking', 'omniform' ) }</strong>{ ' ' }
										{ __( 'to manage and review form entries', 'omniform' ) }
									</Text>
									<Text>
										<strong>{ __( 'Dedicated editor', 'omniform' ) }</strong>{ ' ' }
										{ __( 'for easy customization and sharing', 'omniform' ) }
									</Text>
								</Flex>
							</VStack>
							<Flex justify="space-between">
								<Button variant="secondary" onClick={ () => setModalOpen( false ) }>
									{ __( 'Cancel', 'omniform' ) }
								</Button>
								<Button variant="primary" onClick={ handleConvertForm }>
									{ __( 'Convert Form', 'omniform' ) }
								</Button>
							</Flex>
						</VStack>
					</Modal>
				) }
			</PanelBody>
			<EmailNotificationSettings
				getSetting={ getSetting }
				setSetting={ setSetting }
			/>
			<SubmissionMethodSettings
				getSetting={ getSetting }
				setSetting={ setSetting }
			/>
		</InspectorControls>
	);
}
