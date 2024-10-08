/**
 * The name of the custom post type used to store forms
 *
 * @type {string}
 */
export const POST_TYPE = 'omniform';
export const RESPONSE_POST_TYPE = 'omniform_response';

/**
 * Allowed blocks constant is passed to InnerBlocks precisely as specified here.
 * The contents of the array should never change.
 * The array should contain the name of each block that is allowed.
 *
 * @constant
 * @type {string[]}
 */
export const ALLOWED_BLOCKS = [
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
