/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	Button,
	Flex,
	FlexItem,
	Modal,
	Placeholder,
	Spinner,
	TextControl,
} from '@wordpress/components';
import { useState } from '@wordpress/element';

/**
 * Internal dependencies
 */
import {
	useAlternativeForms,
	useCreateFormFromBlocks,
} from '../utils/hooks';

export default function FormPlaceholder( {
	clientId,
	formId,
	setAttributes,
	onOpenSelectionModal,
} ) {
	const { forms, isResolving } = useAlternativeForms();
	const createFromBlocks = useCreateFormFromBlocks( setAttributes );

	const [ showTitleModal, setShowTitleModal ] = useState( false );
	const [ title, setTitle ] = useState( __( 'Untitled Form', 'omniform' ) );

	const onSubmitForCreation = ( event ) => {
		event.preventDefault();
		createFromBlocks( [], title );
		setShowTitleModal( false );
	};

	return (
		<Placeholder
			icon={ null }
			label={ __( 'OmniForm Form', 'omniform' ) }
			instructions={ __( 'Choose an existing form or create a new one.', 'omniform' ) }
		>
			{ isResolving && <Spinner /> }

			{ ! isResolving && (
				<Button
					variant="primary"
					onClick={ () => console.debug( 'explore templates' ) }
				>
					{ __( 'Explore templates', 'omniform' ) }
				</Button>
			) }

			{ ! isResolving && !! forms.length && (
				<Button
					variant="tertiary"
					onClick={ onOpenSelectionModal }
				>
					{ __( 'Choose existing', 'omniform' ) }
				</Button>
			) }

			{ ! isResolving && (
				<Button
					variant="tertiary"
					onClick={ () => setShowTitleModal( true ) }
				>
					{ __( 'Create new', 'omniform' ) }
				</Button>
			) }

			{ showTitleModal && (
				<Modal
					title={ __( 'Name and create your new form', 'omniform' ) }
					closeLabel={ __( 'Cancel', 'omniform' ) }
					onRequestClose={ () => setShowTitleModal( false ) }
					focusOnMount
				>
					<form onSubmit={ onSubmitForCreation }>
						<TextControl
							label={ __( 'Name', 'omniform' ) }
							value={ title }
							onChange={ setTitle }
						/>
						<Flex justify="flex-end">
							<FlexItem>
								<Button
									variant="primary"
									type="submit"
									disabled={ ! title.length }
									aria-disabled={ ! title.length }
								>
									{ __( 'Create', 'omniform' ) }
								</Button>
							</FlexItem>
						</Flex>
					</form>
				</Modal>
			) }
		</Placeholder>
	);
}
