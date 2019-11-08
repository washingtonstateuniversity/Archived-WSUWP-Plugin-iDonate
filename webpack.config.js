const autoprefixer = require('autoprefixer');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const OptimizeCSSAssetsPlugin = require('optimize-css-assets-webpack-plugin');
const TerserPlugin = require('terser-webpack-plugin');
const path = require('path');

module.exports = env => {
	const DEV = env.NODE_ENV === 'development';

	return {
		context: __dirname,
		entry: {
			jQuery: './includes/jquery.editable.min.js',
			'custom-post-status': './includes/wsuwp-custom-post-status.js',
			'fund-selector': './includes/wsuwp-shortcode-fundselector.js',
			'fund-selector-utils':
				'./includes/wsuwp-shortcode-fundselector-utils.js',
		},
		output: {
			path: path.resolve(__dirname, 'dist'),
			filename: '[name].js',
		},
		mode: DEV ? 'development' : 'production',
		devtool: DEV ? 'inline-source-map' : 'source-map',
		devServer: {
			writeToDisk: true,
			contentBase: path.join(__dirname),
			overlay: true,
			quiet: true,
		},
		module: {
			rules: [
				{
					test: /.less$/,
					use: [
						MiniCssExtractPlugin.loader,
						{
							loader: 'css-loader',
						},
						{
							loader: 'postcss-loader',
							options: {
								ident: 'postcss',
								plugins: () => [
									autoprefixer({
										overrideBrowserslist: [
											'>1%',
											'last 4 versions',
											'Firefox ESR',
											'not ie < 9',
										],
									}),
								],
							},
						},
						'less-loader',
					],
				},
			],
		},
		optimization: {
			minimize: !DEV,
			minimizer: [
				new OptimizeCSSAssetsPlugin({
					cssProcessorOptions: {
						map: {
							inline: false,
							annotation: true,
						},
					},
				}),
				new TerserPlugin({
					terserOptions: {
						compress: {
							warnings: false,
						},
						output: {
							comments: false,
						},
					},
					sourceMap: true,
				}),
			],
		},
		plugins: [
			new MiniCssExtractPlugin({
				filename: '[name].css',
			}),
		],
	};
};
