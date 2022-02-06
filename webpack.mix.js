/**
 * Main dependencies
 */
const mix = require('laravel-mix');
const webpack = require('webpack');

/**
 * ------------------------------
 * Bundle Analyzer
 * to show packages size
 * in localhost:8888
 */
require('laravel-mix-bundle-analyzer'); 
mix.isWatching() ? mix.bundleAnalyzer() : null;
mix.bundleAnalyzer({
  openAnalyzer: false
});

/**
 * ------------------------------
 * Change default output
 * path for bundles
 */
mix.setPublicPath('public_html/');


/**
 * ------------------------------
 * Assets
 */
mix.sass('resources/sass/style.scss', 'public_html/css')
  .sass('resources/sass/landing.scss', 'public_html/css')
  .sass('resources/sass/shop.scss', 'public_html/css')
  .sass('resources/sass/developers.scss', 'public_html/css');

mix.js('resources/js/app.js', 'public_html/js');
mix.minify('public_html/js/app.js');


/**
 * ------------------------------
 * Webpack Config
 */
mix.webpackConfig({
  resolve: {
    extensions: ['.js', '.vue', '.json'],
    alias: {
      // eslint-disable-next-line no-undef
      '@': __dirname + '/resources/js/src'
    },
  },
  plugins: [
    new webpack.optimize.LimitChunkCountPlugin({
      maxChunks: 10
    }),
    new webpack.ContextReplacementPlugin(/moment[/\\]locale$/, /fa|en/),
  ],
  module: {
    rules: [
      {
        enforce: 'pre',
        exclude: /node_modules/,
        loader: 'eslint-loader',
        test: /\.(js|vue)?$/
      },
    ]
  }, 
  output: {
    chunkFilename: 'jschunks/[name].[contenthash].js',
  }
});
