const webpack = require('webpack');
const path = require( 'path' );
const TerserPlugin = require( 'terser-webpack-plugin' )
/*eslint-disable */
const { join } = require( 'path' );

/**
 * Given a string, returns a new string with dash separators converted to
 * camel-case equivalent. This is not as aggressive as `_.camelCase`, which
 * which would also upper-case letters following numbers.
 *
 * @param {string} string Input dash-delimited string.
 *
 * @return {string} Camel-cased string.
 */
const camelCaseDash = string => string.replace( /-([a-z])/g, ( match, letter ) => letter.toUpperCase() );

/**
 * Define externals to load components through the wp global.
 */
const externals = [
	// 'components',
	// 'edit-post',
	// 'element',
	// 'plugins',
	// 'editor',
	// 'blocks',
	// 'hooks',
	// 'utils',
	// 'date',
	// 'data',
	'i18n',
].reduce(
	( externals, name ) => ( {
		...externals,
		[ `@wordpress/${ name }` ]: `wp.${ camelCaseDash( name ) }`,
	} ),
	{
		wp: 'wp',
		ga: 'ga', // Old Google Analytics.
		gtag: 'gtag', // New Google Analytics.
		react: 'React', // React itself is there in Gutenberg.
		jquery: 'jQuery', // import $ from 'jquery' // Use the WordPress version after enqueuing it.
		lodash: 'lodash',
		moment: 'moment',
		'react-dom': 'ReactDOM',
	}
);
/*eslint-enable */

module.exports = ( env, argv ) => {
	const isDev = ( env.MODE === 'development' );
	return {
		entry: {
			'./assets/js/scripts.js': ['./src/js/scripts.js', './src/scss/styles.scss'],
			'./assets/js/admin.js': ['./src/js/admin.js', './src/scss/admin.scss'],
			// styles: './src/scss/styles.scss',
			// admin: './src/scss/admin.scss',
		},
		output: {
			// Add /* filename */ comments to generated require()s in the output.
			pathinfo: true,
			// [name] allows for the entry object keys to be used as file names.
			// filename: ( chunkData ) => {
			// 	return -1 === chunkData.chunk.name.indexOf( 'styles' ) ? 'js/[name].js' : 'css/[name].css';
			// },
			// filename: '[ext]/[name].[ext]',
			filename: '[name]',
			// Specify the path to the JS files.
			// path: path.resolve( __dirname, 'assets' ),
			path: path.resolve( __dirname ),
		},
		// Setup a loader to transpile down the latest and great JavaScript so older browsers can understand it.
		module: {
			rules: [
				{
					// Look for any .js files.
					test: /\.js$/,
					// Exclude the node_modules folder.
					exclude: /node_modules/,
					// Use babel loader to transpile the JS files.
					use: {
						loader: 'babel-loader',
						options: {
							"presets": [
								["@babel/preset-env", {"modules": false}]
							]
						}
					}
				},
				{
					test: /\.scss$/,
					exclude: /node_modules/,
					use: [
						{
							loader: 'file-loader',
							options: {
								outputStyle: 'compressed',
								name: './assets/css/[name].css',
							},
						},
						{ loader: 'extract-loader' },
						{ loader: 'css-loader?-url' },
						{ loader: 'postcss-loader' },
						{ loader: isDev ? 'sass-loader?sourceMap' : 'sass-loader' },
					],
				},
			],
		},
		optimization: {
			minimize: ! isDev,
			chunkIds: 'named',
			minimizer: [ new TerserPlugin( {
				extractComments: /^\**!|@preserve|@license|@cc_on/i,
			} ) ],
		},
		stats: 'verbose',
		// stats: 'errors-only',
		// Add externals.
		externals,
		plugins: [
			// Provides jQuery for other JS bundled with Webpack
			new webpack.ProvidePlugin({
				$: 'jquery',
				jQuery: 'jquery'
			})
		]
	};
};
