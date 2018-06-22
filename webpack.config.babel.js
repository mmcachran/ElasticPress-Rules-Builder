import path from 'path';
import webpack from 'webpack';

const DIST_PATH = path.resolve('./dist/js');

const nodeEnv = process.env.NODE_ENV || 'development';
const isProduction = 'production' === nodeEnv;

const config = {
	cache: true,
	output: {
		path: DIST_PATH,
		filename: '[name].min.js',
	},
	resolve: {
		modules: ['node_modules'],
	},
	module: {
		rules: [
			{
				test: /\.js$/,
				enforce: 'pre',
				loader: 'eslint-loader',
				query: {
					configFile: './.eslintrc'
				}
			},
			{
				test: /\.js$/,
				use: [{
					loader: 'babel-loader',
					options: {
						babelrc: false,
						presets: [
							'es2015'
						]
					}

				}]
			}
		]
	},
	plugins: [
		new webpack.NoEmitOnErrorsPlugin(),

		new webpack.DefinePlugin({
			'process.env': {
				NODE_ENV: JSON.stringify(nodeEnv)
			}
		}),
	],
	stats: { colors: true },
};

// uglify plugin
if (isProduction) {
	config.plugins.push(
		new webpack.optimize.UglifyJsPlugin({
			compress: { warnings: false },
			output: { comments: false },
			sourceMap: true
		})
	);
}

module.exports = config;
