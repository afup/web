// webpack.config.js
const webpack = require('webpack')
const CopyWebpackPlugin = require('copy-webpack-plugin');
const CleanWebpackPlugin = require('clean-webpack-plugin'); //installed via npm
const ExtractTextPlugin = require('extract-text-webpack-plugin');
const path = require('path');

const adminConfig = {
  context: __dirname,
  entry: {
    "admin": './sources/admin.js',
    "techletter": './htdocs/js/techletter/techletter.js'
  },
  output: {
    path: path.resolve(__dirname, 'htdocs/js_dist'),
    filename: '[name].js'
  },
  plugins: [
    new CleanWebpackPlugin([
      path.resolve(__dirname, 'htdocs/assets/calendrier/'),
      path.resolve(__dirname, 'htdocs/js_dist')
    ]),
    new CopyWebpackPlugin([
      { from: path.resolve(__dirname, 'node_modules/tablefilter/dist') },
      { from: path.resolve(__dirname, 'node_modules/jquery/dist/jquery.min.js'), to: path.resolve(__dirname, 'htdocs/assets/calendrier/js/jquery.min.js') },
      { from: path.resolve(__dirname, 'node_modules/angular/lib/angular.min.js'), to: path.resolve(__dirname, 'htdocs/assets/calendrier/js/angular.min.js') },
      { from: path.resolve(__dirname, 'node_modules/bootstrap/dist/js/bootstrap.min.js'), to: path.resolve(__dirname, 'htdocs/assets/calendrier/js/bootstrap.min.js') },
      { from: path.resolve(__dirname, 'node_modules/bootswatch/yeti/bootstrap.min.css'), to: path.resolve(__dirname, 'htdocs/assets/calendrier/css/bootstrap.min.css') },
      { from: path.resolve(__dirname, 'node_modules/bootswatch/fonts/'), to: path.resolve(__dirname, 'htdocs/assets/calendrier/fonts/') },
      { from: path.resolve(__dirname, 'node_modules/notyf/dist/'), to: path.resolve(__dirname, 'htdocs/assets/techletter/') }
    ])
  ],
  module: {
    rules: [
      {
        test: /\.js$/,
        include: path.resolve(__dirname, './'),
        use: [{
          loader: 'babel-loader',
          options: {
            presets: [
              ['es2015', { modules: false }]
            ]
          }
        }]
      }
    ]
  }
}

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

module.exports = [adminConfig, siteConfig]
