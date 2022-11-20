/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';
import {
	__experimentalRecursionProvider as RecursionProvider,
	__experimentalUseHasRecursion as useHasRecursion,
	store as blockEditorStore,
	useBlockProps,
	Warning,
} from '@wordpress/block-editor';
import {
	Spinner,
} from '@wordpress/components';
import { store as coreStore } from '@wordpress/core-data';

/**
 * Internal dependencies
 */
import FormPlaceholder from './placeholder';
import FormInnerBlocks from './inner-blocks';
import FormInspectorControls from './inspector-controls';

export default function FormEdit( {
	attributes,
	setAttributes,
	clientId,
	isSelected,
} ) {
	const { ref } = attributes;
	const hasAlreadyRendered = useHasRecursion( ref );

	const { isResolved, innerBlocks, isMissing } = useSelect(
		( select ) => {
			const { getEditedEntityRecord, hasFinishedResolution } = select( coreStore );
			const { getBlocks } = select( blockEditorStore );

			const getEntityArgs = [
				'postType',
				'inquirywp_form',
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
		</>
	);
}
