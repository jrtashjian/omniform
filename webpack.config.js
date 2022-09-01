/**
 * WordPress dependencies.
 */
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const { camelCaseDash } = require( '@wordpress/dependency-extraction-webpack-plugin/lib/util' );

/**
 * Internal dependencies.
 */
const { dependencies } = require( './package' );
const PLUGIN_NAMESPACE = '@pluginwp/';

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
					name: [ 'pluginwp', camelCaseDash( packageName ) ],
					type: 'window',
				},
			},
		};
	}, {} ),
};
