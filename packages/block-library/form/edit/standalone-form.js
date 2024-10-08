/**
 * WordPress dependencies
 */
import {
	useBlockProps,
	useInnerBlocksProps,
	InnerBlocks,
	Warning,
	store as blockEditorStore,
} from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import FormInspectorControls from './inspector-controls';
import { ALLOWED_BLOCKS } from '../../shared/constants';

const TEMPLATE = [
	[ 'core/group', {
		tagName: 'div',
		align: 'full',
		layout: {
			type: 'constrained',
		},
	}, [
		[ 'omniform/response-notification', {
			messageContent: __( 'Success! Your submission has been completed.', 'omniform' ),
			className: 'is-style-success',
		}, [] ],
		[ 'omniform/response-notification', {
			messageContent: __( 'Unfortunately, your submission was not successful. Please ensure all fields are correctly filled out and try again.', 'omniform' ),
			className: 'is-style-error',
		}, [] ],
		[ 'core/paragraph', {
			content: __( "If you have any questions or comments, or if you'd like to work with me or collaborate on a project, please don't hesitate to get in touch. I look forward to hearing from you!", 'omniform' ),
			dropCap: false,
		}, [] ],
		[ 'omniform/field', {
			fieldLabel: __( 'Your email address', 'omniform' ),
			fieldName: 'your-email-address',
		}, [
			[ 'omniform/label', {}, [] ],
			[ 'omniform/input', {}, [] ],
		] ],
		[ 'omniform/field', {
			fieldLabel: __( 'Your message', 'omniform' ),
			fieldName: 'your-message',
		}, [
			[ 'omniform/label', {}, [] ],
			[ 'omniform/textarea', {}, [] ],
		] ],
		[ 'core/group', {
			tagName: 'div',
			layout: {
				type: 'flex',
				flexWrap: 'nowrap',
			},
		}, [
			[ 'omniform/button', {
				buttonType: 'submit',
				buttonLabel: __( 'Send Message', 'omniform' ),
			}, [] ],
		] ],
	] ],
];

export default function StandaloneForm( blockObject ) {
	const {
		name,
		clientId,
	} = blockObject;

	const { isNested, hasInnerBlocks } = useSelect(
		( select ) => {
			const {
				getBlockParents,
				getBlock,
				getBlockCount,
			} = select( blockEditorStore );

			const parentBlocks = getBlockParents( clientId );
			const rootBlock = getBlock( parentBlocks[ 0 ] );

			return {
				isNested: rootBlock?.name === name,
				hasInnerBlocks: getBlockCount( clientId ) > 0,
			};
		},
		[ clientId, name ]
	);

	const blockProps = useBlockProps( {
		className: 'block-library-block__reusable-block-container',
	} );

	const innerBlockProps = useInnerBlocksProps( blockProps, {
		allowedBlocks: ALLOWED_BLOCKS,
		template: TEMPLATE,
		renderAppender: hasInnerBlocks
			? undefined
			: InnerBlocks.ButtonBlockAppender,
	} );

	if ( isNested ) {
		return (
			<div { ...blockProps }>
				<Warning>
					{ __( 'A form cannot be nested within another form.', 'omniform' ) }
				</Warning>
			</div>
		);
	}

	return (
		<>
			<FormInspectorControls blockObject={ blockObject } />
			<div { ...innerBlockProps } />
		</>
	);
}
