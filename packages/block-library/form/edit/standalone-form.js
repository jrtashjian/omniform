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

/**
 * Allowed blocks constant is passed to InnerBlocks precisely as specified here.
 * The contents of the array should never change.
 * The array should contain the name of each block that is allowed.
 *
 * @constant
 * @type {string[]}
 */
const ALLOWED_BLOCKS = [
	'omniform/button',
	'omniform/captcha',
	'omniform/conditional-group',
	'omniform/field',
	'omniform/fieldset',
	'omniform/hidden',
	'omniform/input',
	'omniform/label',
	'omniform/post-comments-form-cancel-reply-link',
	'omniform/post-comments-form-title',
	'omniform/response-notification',
	'omniform/select-group',
	'omniform/select-option',
	'omniform/select',
	'omniform/textarea',
	'core/audio',
	'core/block',
	'core/code',
	'core/column',
	'core/columns',
	'core/cover',
	'core/file',
	'core/gallery',
	'core/group',
	'core/heading',
	'core/image',
	'core/list-item',
	'core/list',
	'core/missing',
	'core/paragraph',
	'core/pattern',
	'core/preformatted',
	'core/separator',
	'core/site-logo',
	'core/site-tagline',
	'core/site-title',
	'core/spacer',
	'core/table',
	'core/video',
];

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
