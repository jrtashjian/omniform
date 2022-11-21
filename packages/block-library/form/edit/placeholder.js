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
} ) {
	const { forms, isResolving } = useAlternativeForms();
	const createFromBlocks = useCreateFormFromBlocks( setAttributes );

	const [ showTitleModal, setShowTitleModal ] = useState( false );
	const [ title, setTitle ] = useState( __( 'Untitled Form', 'inquirywp' ) );

	const onSubmitForCreation = ( event ) => {
		event.preventDefault();
		createFromBlocks( [], title );
		setShowTitleModal( false );
	};

	return (
		<Placeholder
			icon={ null }
			label={ __( 'InquiryWP Form', 'inquirywp' ) }
			instructions={ __( 'Choose an existing form or create a new one.', 'inquirywp' ) }
		>
			{ isResolving && <Spinner /> }

			{ ! isResolving && !! forms.length && (
				<Button
					variant="primary"
					onClick={ () => console.debug( 'choose' ) }
				>
					{ __( 'Choose', 'inquirywp' ) }
				</Button>
			) }

			{ ! isResolving && (
				<Button
					variant="secondary"
					onClick={ () => setShowTitleModal( true ) }
				>
					{ __( 'Start blank', 'inquirywp' ) }
				</Button>
			) }

			{ showTitleModal && (
				<Modal
					title={ __( 'Name and create your new form', 'inquirywp' ) }
					closeLabel={ __( 'Cancel', 'inquirywp' ) }
					onRequestClose={ () => setShowTitleModal( false ) }
				>
					<form onSubmit={ onSubmitForCreation }>
						<TextControl
							label={ __( 'Name', 'inquirywp' ) }
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
									{ __( 'Create', 'inquirywp' ) }
								</Button>
							</FlexItem>
						</Flex>
					</form>
				</Modal>
			) }
		</Placeholder>
	);
}