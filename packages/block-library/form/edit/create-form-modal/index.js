/**
 * WordPress dependencies
 */
import {
	Icon,
	BaseControl,
	TextControl,
	Flex,
	FlexItem,
	FlexBlock,
	Button,
	Modal,
	__experimentalRadioGroup as RadioGroup,
	__experimentalRadio as Radio,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';
import { useInstanceId } from '@wordpress/compose';
import { check } from '@wordpress/icons';

import './index.scss';
import { fieldGroup, typeLock, typeSearch } from '../../../shared/icons';

export default function CreateFormModal( { closeModal, onCreate } ) {
	const [ title, setTitle ] = useState( '' );
	const [ type, setArea ] = useState( 'uncategorized' );
	const [ isSubmitting, setIsSubmitting ] = useState( false );
	const instanceId = useInstanceId( CreateFormModal );

	const formTypes = [
		{
			type: 'uncategorized',
			description: __( 'General forms are used to collect and organize information for processing or storage.', 'omniform' ),
			icon: fieldGroup,
			label: __( 'General', 'omniform' ),
		},
		{
			type: 'search',
			description: __( 'A search form allows users to enter a search query and find relevant information on the website.', 'omniform' ),
			icon: typeSearch,
			label: (
				<>
					{ __( 'Search', 'omniform' ) }
					<span className="badge">{ __( 'coming soon', 'omniform' ) }</span>
				</>
			),
			disabled: true,
		},
		{
			type: 'login',
			description: __( 'A login form is used to authenticate a user\'s identity and grant them access to the website.', 'omniform' ),
			icon: typeLock,
			label: (
				<>
					{ __( 'Login', 'omniform' ) }
					<span className="badge">{ __( 'coming soon', 'omniform' ) }</span>
				</>
			),
			disabled: true,
		},
	];

	return (
		<Modal
			title={ __( 'Create a form', 'omniform' ) }
			closeLabel={ __( 'Close', 'omniform' ) }
			onRequestClose={ closeModal }
			overlayClassName="omniform-create-form-modal"
		>
			<form
				onSubmit={ async ( event ) => {
					event.preventDefault();
					if ( ! title ) {
						return;
					}
					setIsSubmitting( true );
					await onCreate( { title, type } );
				} }
			>
				<TextControl
					label={ __( 'Name', 'omniform' ) }
					value={ title }
					onChange={ setTitle }
					required
				/>
				<BaseControl
					label={ __( 'Type', 'omniform' ) }
					id={ `omniform-create-form-modal__area-selection-${ instanceId }` }
					className="omniform-create-form-modal__area-base-control"
				>
					<RadioGroup
						label={ __( 'Type', 'omniform' ) }
						className="omniform-create-form-modal__area-radio-group"
						id={ `omniform-create-form-modal__area-selection-${ instanceId }` }
						onChange={ setArea }
						checked={ type }
					>
						{ formTypes.map(
							( { icon, label, type: value, description, disabled } ) => (
								<Radio
									key={ label }
									value={ value }
									className="omniform-create-form-modal__area-radio"
									disabled={ disabled }
								>
									<Flex align="start" justify="start">
										<FlexItem>
											<Icon icon={ icon } />
										</FlexItem>
										<FlexBlock className="omniform-create-form-modal__option-label">
											{ label }
											<div>{ description }</div>
										</FlexBlock>

										<FlexItem className="omniform-create-form-modal__checkbox">
											{ type === value && (
												<Icon icon={ check } />
											) }
										</FlexItem>
									</Flex>
								</Radio>
							)
						) }
					</RadioGroup>
				</BaseControl>
				<Flex
					className="omniform-create-form-modal__modal-actions"
					justify="flex-end"
				>
					<FlexItem>
						<Button
							variant="secondary"
							onClick={ () => {
								closeModal();
							} }
						>
							{ __( 'Cancel', 'omniform' ) }
						</Button>
					</FlexItem>
					<FlexItem>
						<Button
							variant="primary"
							type="submit"
							disabled={ ! title }
							isBusy={ isSubmitting }
						>
							{ __( 'Create', 'omniform' ) }
						</Button>
					</FlexItem>
				</Flex>
			</form>
		</Modal>
	);
}
