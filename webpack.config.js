// webpack.config.js
const webpack = require('webpack')
const ExtractTextPlugin = require('extract-text-webpack-plugin');
const path = require('path');

const siteConfig = {
  context: __dirname,
  entry: path.resolve(__dirname, 'htdocs/templates/site/scss/styles.scss'),
  output: {
    path: path.resolve(__dirname, 'htdocs/templates/site/css'),
    filename: 'styles.css',
  },
  plugins: [
    new ExtractTextPlugin('styles.css'),
  ],
  module: {
    rules: [
      {
        test: /\.scss$/,
        use: ExtractTextPlugin.extract({
          fallback: 'style-loader',
          use: ['css-loader?url=false', 'sass-loader'],
        }),
      },
      {
        test: /\.(ttf|eot|svg|gif|png|jpg)$/,
        loader: 'file-loader',
      }
    ]
  }
}

module.exports = [siteConfig]
