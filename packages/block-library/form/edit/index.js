/**
 * WordPress dependencies
 */
import {
	store as blockEditorStore,
	useBlockProps,
	Warning,
	__experimentalRecursionProvider as RecursionProvider,
	__experimentalUseHasRecursion as useHasRecursion,
} from '@wordpress/block-editor';
import {
	Modal,
	Placeholder,
	Spinner,
} from '@wordpress/components';
import { store as coreStore } from '@wordpress/core-data';
import { useSelect } from '@wordpress/data';
import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { FORM_POST_TYPE } from '../../shared/constants';
import { form } from '../../shared/icons';
import FormInnerBlocks from './inner-blocks';
import FormInspectorControls from './inspector-controls';
import FormPlaceholder from './placeholder';
import FormSelectionModal from './selection-modal';

export default function FormEdit( {
	context,
	attributes,
	setAttributes,
	clientId,
} ) {
	const { postId: contextPostId, postType: contextPostType } = context;
	const { ref } = attributes;

	const entityId = ( contextPostType === FORM_POST_TYPE )
		? contextPostId
		: ref;

	const hasAlreadyRendered = useHasRecursion( entityId );
	const [ isFormSelectionOpen, setIsFormSelectionOpen ] = useState( false );

	const { isResolved, innerBlocks, isMissing } = useSelect(
		( select ) => {
			const { getEditedEntityRecord, hasFinishedResolution } = select( coreStore );
			const { getBlocks } = select( blockEditorStore );

			const getEntityArgs = [
				'postType',
				FORM_POST_TYPE,
				entityId,
			];
			const entityRecord = entityId
				? getEditedEntityRecord( ...getEntityArgs )
				: null;
			const hasResolvedEntity = entityId
				? hasFinishedResolution( 'getEditedEntityRecord', getEntityArgs )
				: false;

			return {
				innerBlocks: getBlocks( clientId ),
				isResolved: hasResolvedEntity,
				isMissing: hasResolvedEntity && ! entityRecord,
			};
		},
		[ entityId, clientId, contextPostId, contextPostType ]
	);

	const blockProps = useBlockProps( {
		className: 'block-library-block__reusable-block-container',
	} );
	const isPlaceholder = ! entityId;
	const isEntityAvailable = ! isPlaceholder && ! isMissing && isResolved;

	if (
		JSON.stringify( context ) === JSON.stringify( {} ) &&
		typeof ref === 'undefined'
	) {
		return (
			<div { ...blockProps }>
				<Placeholder
					icon={ form }
					label={ __( 'OmniForm', 'omniform' ) }
					instructions={ __( 'A single form will be rendered here.', 'omniform' ) }
				/>
			</div>
		);
	}

	if ( innerBlocks.length === 0 && isMissing ) {
		return (
			<div { ...blockProps }>
				<Warning>
					{ __( 'Form has been deleted or is unavailable.', 'omniform' ) }
				</Warning>
			</div>
		);
	}

	if ( isEntityAvailable && hasAlreadyRendered ) {
		return (
			<div { ...blockProps }>
				<Warning>
					{ __( 'Form cannot be rendered inside itself.', 'omniform' ) }
				</Warning>
			</div>
		);
	}

	return (
		<>
			<RecursionProvider uniqueId={ entityId }>
				<FormInspectorControls
					formId={ entityId }
					isEntityAvailable={ isEntityAvailable }
				/>

				{ isPlaceholder && (
					<div { ...blockProps }>
						<FormPlaceholder
							clientId={ clientId }
							formId={ entityId }
							setAttributes={ setAttributes }
							onOpenSelectionModal={ () => setIsFormSelectionOpen( true ) }
						/>
					</div>
				) }

				{ isEntityAvailable && (
					<FormInnerBlocks
						blockProps={ blockProps }
						formId={ entityId }
						hasInnerBlocks={ innerBlocks.length > 0 }
					/>
				) }

				{ ! isPlaceholder && ! isResolved && (
					<div { ...blockProps }>
						<Spinner />
					</div>
				) }
			</RecursionProvider>
			{ isFormSelectionOpen && (
				<Modal
					overlayClassName="block-editor-form__selection-modal"
					title={ __( 'Choose a Form', 'omniform' ) }
					closeLabel={ __( 'Cancel', 'omniform' ) }
					onRequestClose={ () => setIsFormSelectionOpen( false ) }
				>
					<FormSelectionModal
						formId={ entityId }
						setAttributes={ setAttributes }
						onClose={ () => setIsFormSelectionOpen( false ) }
					/>
				</Modal>
			) }
		</>
	);
}
