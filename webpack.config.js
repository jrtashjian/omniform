/**
 * WordPress dependencies.
 */
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const { camelCaseDash } = require( '@wordpress/dependency-extraction-webpack-plugin/lib/util' );

/**
 * Internal dependencies.
 */
const { dependencies } = require( './package' );
const PLUGIN_NAMESPACE = '@inquirywp/';

const pluginPackages = Object.keys( dependencies )
	.filter( ( packageName ) => packageName.startsWith( PLUGIN_NAMESPACE ) )
	.map( ( packageName ) => packageName.replace( PLUGIN_NAMESPACE, '' ) );

module.exports = {
	...defaultConfig,

	entry: pluginPackages.reduce( ( memo, packageName ) => {
		return {
			...memo,
			[ packageName ]: {
				import: `./packages/${ packageName }`,
				library: {
					name: [ 'inquirywp', camelCaseDash( packageName ) ],
					type: 'window',
				},
			},
		};
	}, {
		'block-library/form': './packages/block-library/form',
		'block-library/view-form': './packages/block-library/form/view.js',
		'block-library/field-text': './packages/block-library/field-text',
	} ),
};
