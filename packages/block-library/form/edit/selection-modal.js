/**
 * WordPress dependencies
 */
import { __experimentalBlockPatternsList as BlockPatternsList } from '@wordpress/block-editor';
import { createBlock, parse } from '@wordpress/blocks';
import { __experimentalHStack as HStack } from '@wordpress/components';
import { useAsyncList } from '@wordpress/compose';
import { useCallback, useMemo } from '@wordpress/element';
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
				blocks: createBlock( 'inquirywp/form', { ref: form.id } ), // parse( form.content.raw ),
				form,
			};
		} );
		return formsAsPatterns;
	}, forms );
	const shownForms = useAsyncList( filteredForms );

	const onFormSelect = useCallback( ( form ) => {
		setAttributes( { ref: form.id } );
		onClose();
	}, [] );

	const hasForms = !! forms.length;

	return (
		<div className="block-library-form__selection-content">
			{ hasForms && (
				<div>
					<h2>{ __( 'Existing forms', 'inquirywp' ) }</h2>
					<BlockPatternsList
						blockPatterns={ filteredForms }
						shownPatterns={ shownForms }
						onClickPattern={ ( pattern ) => {
							onFormSelect( pattern.form );
						} }
						orientation="horizontal"
					/>
				</div>
			) }

			{ ! hasForms && (
				<HStack alignment="center">
					<p>{ __( 'No results found.', 'inquirywp' ) }</p>
				</HStack>
			) }
		</div>
	);
}
