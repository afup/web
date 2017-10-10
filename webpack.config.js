// webpack.config.js
const webpack = require('webpack')
const CopyWebpackPlugin = require('copy-webpack-plugin');
const path = require('path')

const config = {
	context: path.resolve(__dirname, 'sources'),
	entry: {
		"admin": './admin.js'
	},
	output: {
		path: path.resolve(__dirname, 'htdocs/js_dist'),
		filename: '[name].js'
	},
	plugins: [
		new CopyWebpackPlugin([
			{ from: path.resolve(__dirname, 'node_modules/tablefilter/dist') },
		])
	]
	/*,
	module: {
		rules: [{
			test: /\.js$/,
			include: path.resolve(__dirname, 'src'),
			use: [{
				loader: 'babel-loader',
				options: {
					presets: [
						['es2015', { modules: false }]
					]
				}
			}]
		}]
	}*/
}

module.exports = config
