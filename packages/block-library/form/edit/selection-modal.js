/**
 * WordPress dependencies
 */
import { __experimentalBlockPatternsList as BlockPatternsList } from '@wordpress/block-editor';
import { createBlock } from '@wordpress/blocks';
import { __experimentalHStack as HStack } from '@wordpress/components';
import { useAsyncList } from '@wordpress/compose';
import { useMemo } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { useAlternativeForms } from '../utils/hooks';

export default function FormSelectionModal( {
	formId = null,
	onClose,
	setAttributes,
} ) {
	const { forms } = useAlternativeForms( formId );
	const filteredForms = useMemo( () => {
		const formsAsPatterns = forms.map( ( form ) => {
			return {
				name: form.slug,
				title: form.title.raw,
				blocks: createBlock( 'omniform/form', { ref: form.id } ),
				viewportWidth: 768,
				form,
			};
		} );
		return formsAsPatterns;
	}, forms ); // eslint-disable-line react-hooks/exhaustive-deps -- passing `[ forms ]` is causing weird rendering behavior.
	const shownForms = useAsyncList( filteredForms );

	const onFormSelect = ( form ) => {
		setAttributes( { ref: form.id } );
		onClose();
	};

	const hasForms = !! forms.length;

	return (
		<div className="block-library-form__selection-content">
			{ hasForms && (
				<BlockPatternsList
					blockPatterns={ filteredForms }
					shownPatterns={ shownForms }
					onClickPattern={ ( pattern ) => {
						onFormSelect( pattern.form );
					} }
					orientation="horizontal"
				/>
			) }

			{ ! hasForms && (
				<HStack alignment="center">
					<p>{ __( 'No results found.', 'omniform' ) }</p>
				</HStack>
			) }
		</div>
	);
}
