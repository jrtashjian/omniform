/**
 * WordPress dependencies.
 */
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const { camelCaseDash } = require( '@wordpress/dependency-extraction-webpack-plugin/lib/util' );

const pluginPackages = [ 'dashboard' ];

module.exports = {
	...defaultConfig,

	entry: {
		...pluginPackages.reduce( ( memo, packageName ) => {
			return {
				...memo,
				[ `${ packageName }/index` ]: {
					import: `./packages/${ packageName }`,
					library: {
						name: [ 'omniform', camelCaseDash( packageName ) ],
						type: 'window',
					},
				},
			};
		}, {} ),
		...defaultConfig.entry(),
	},
};
