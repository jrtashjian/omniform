/**
 * WordPress dependencies
 */
import {
	store as blockEditorStore,
	useBlockProps,
	Warning, __experimentalRecursionProvider as RecursionProvider,
	__experimentalUseHasRecursion as useHasRecursion,
} from '@wordpress/block-editor';
import {
	Modal, Spinner,
} from '@wordpress/components';
import { store as coreStore } from '@wordpress/core-data';
import { useSelect } from '@wordpress/data';
import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { FORM_POST_TYPE } from '../../shared/constants';
import FormInnerBlocks from './inner-blocks';
import FormInspectorControls from './inspector-controls';
import FormPlaceholder from './placeholder';
import FormSelectionModal from './selection-modal';

export default function FormEdit( {
	attributes,
	setAttributes,
	clientId,
} ) {
	const { ref } = attributes;
	const hasAlreadyRendered = useHasRecursion( ref );
	const [ isFormSelectionOpen, setIsFormSelectionOpen ] = useState( false );

	const { isResolved, innerBlocks, isMissing } = useSelect(
		( select ) => {
			const { getEditedEntityRecord, hasFinishedResolution } = select( coreStore );
			const { getBlocks } = select( blockEditorStore );

			const getEntityArgs = [
				'postType',
				FORM_POST_TYPE,
				ref,
			];
			const entityRecord = ref
				? getEditedEntityRecord( ...getEntityArgs )
				: null;
			const hasResolvedEntity = ref
				? hasFinishedResolution( 'getEditedEntityRecord', getEntityArgs )
				: false;

			return {
				innerBlocks: getBlocks( clientId ),
				isResolved: hasResolvedEntity,
				isMissing: hasResolvedEntity && ! entityRecord,
			};
		},
		[ ref, clientId ]
	);

	const blockProps = useBlockProps( {
		className: 'block-library-block__reusable-block-container is-layout-flow',
	} );
	const isPlaceholder = ! ref;
	const isEntityAvailable = ! isPlaceholder && ! isMissing && isResolved;

	if ( innerBlocks.length === 0 && isMissing ) {
		<div { ...blockProps }>
			<Warning>
				{ __( 'Form has been deleted or is unavailable.', 'inquirywp' ) }
			</Warning>
		</div>;
	}

	if ( isEntityAvailable && hasAlreadyRendered ) {
		return (
			<div { ...blockProps }>
				<Warning>
					{ __( 'Form cannot be rendered inside itself.', 'inquirywp' ) }
				</Warning>
			</div>
		);
	}

	return (
		<>
			<RecursionProvider uniqueId={ ref }>
				<FormInspectorControls
					formId={ ref }
					isEntityAvailable={ isEntityAvailable }
				/>

				{ isPlaceholder && (
					<div { ...blockProps }>
						<FormPlaceholder
							clientId={ clientId }
							formId={ ref }
							setAttributes={ setAttributes }
							onOpenSelectionModal={ () => setIsFormSelectionOpen( true ) }
						/>
					</div>
				) }

				{ isEntityAvailable && (
					<FormInnerBlocks
						blockProps={ blockProps }
						formId={ ref }
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
					title={ __( 'Choose a Form', 'inquirywp' ) }
					closeLabel={ __( 'Cancel', 'inquirywp' ) }
					onRequestClose={ () => setIsFormSelectionOpen( false ) }
				>
					<FormSelectionModal
						formId={ ref }
						setAttributes={ setAttributes }
						onClose={ () => setIsFormSelectionOpen( false ) }
					/>
				</Modal>
			) }
		</>
	);
}
