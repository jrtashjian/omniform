/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	Button,
	Placeholder,
	Spinner,
} from '@wordpress/components';
import { useDispatch } from '@wordpress/data';
import { useState } from '@wordpress/element';
import { store as noticesStore } from '@wordpress/notices';

/**
 * Internal dependencies
 */
import {
	useAlternativeForms,
	useCreateFormFromBlocks,
} from '../utils/hooks';
import CreateFormModal from './create-form-modal';

export default function FormPlaceholder( {
	setAttributes,
	onOpenSelectionModal,
} ) {
	const { forms, isResolving } = useAlternativeForms();
	const createFromBlocks = useCreateFormFromBlocks( setAttributes );

	const [ isModalOpen, setIsModalOpen ] = useState( false );
	const { createSuccessNotice, createErrorNotice } = useDispatch( noticesStore );

	const createForm = async ( { title, type } ) => {
		if ( ! title ) {
			createErrorNotice( __( 'Title is not defined', 'omniform' ), { type: 'snackbar' } );
			return;
		}

		try {
			await createFromBlocks( [], title, type );
			createSuccessNotice( __( 'Form created', 'omniform' ), { type: 'snackbar' } );
			setIsModalOpen( false );
		} catch ( error ) {
			const errorMessage =
				error.message && error.code !== 'unknown_error'
					? error.message
					: __( 'An error occurred while creating the form.', 'omniform' );

			createErrorNotice( errorMessage, { type: 'snackbar' } );
			setIsModalOpen( false );
		}
	};

	return (
		<Placeholder
			icon={ null }
			label={ __( 'OmniForm Form', 'omniform' ) }
			instructions={ __( 'Choose an existing form or create a new one.', 'omniform' ) }
		>
			{ isResolving && <Spinner /> }

			{ ! isResolving && !! forms.length && (
				<Button
					variant="primary"
					onClick={ onOpenSelectionModal }
				>
					{ __( 'Choose', 'omniform' ) }
				</Button>
			) }

			{ ! isResolving && (
				<Button
					variant="secondary"
					onClick={ () => setIsModalOpen( true ) }
				>
					{ __( 'Start blank', 'omniform' ) }
				</Button>
			) }

			{ isModalOpen && (
				<CreateFormModal
					closeModal={ () => setIsModalOpen( false ) }
					onCreate={ createForm }
				/>
			) }
		</Placeholder>
	);
}
