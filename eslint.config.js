const wordpress = require( '@wordpress/eslint-plugin' );

module.exports = [
	{
		ignores: [
			'**/build/**',
			'**/node_modules/**',
			'**/vendor/**',
			'**/vendor_prefixed/**',
			'**/phpunit/coverage/**',
		],
	},
	...wordpress.configs[ 'recommended-with-formatting' ],
	{
		rules: {
			'@wordpress/no-unsafe-wp-apis': 'off',
		},
	},
];
