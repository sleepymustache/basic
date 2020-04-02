/**
 * This is the configuration file for webpack. It is designed to work side-by-side with gulp. We do
 * not use webpack as a task runner, instead us gulp.
*/

const webpack = require('webpack');

module.exports = {
  /**
   * This is the entry point
   */
  entry: './src/js/main.js',

  /**
   * We enable source maps, but do not reference it in the bundle to save space
   *
   * Prod: none
   * Dev:  source-map
   */
  devtool: 'source-map',

  /**
   * The name of the bundle
   */
  output: {
    filename: 'main.bundle.js'
  },
  module: {
    rules: [{
      /**
       * We look for all JS files, and optionally JSX files
       */
      test: /\.jsx?$/,

      /**
       * Ignore all the npm files otherwise it'd be super big
       */
      exclude: /(node_modules|bower_components)/,

      /**
       * When the test is matched, use babel-loader to transplile to ES5
       */
      use: {
        loader: 'babel-loader',
        options: {
          /**
           * Enable Caching can speed up transpiling by 2x
           */
          cacheDirectory: true,

          /**
           * This preset replaces the old ES2015 prest
           */
          presets: ['babel-preset-env'],

          /**
           * This will make your code smaller by adding all the polyfills at once at
           * the top of the JS File. If you don't use much JS, then comment this out
           * as you probably don't want all the polyfills
           */
          plugins: ['transform-runtime'],
        }
      }
    }],
  },

  /**
   * Enable parallel processing of JS
   */
  parallelism: 4,


  plugins: [
    /**
     * Uglify the output, but still generate a sitemap
     */
    new webpack.optimize.UglifyJsPlugin({
      parallel: true,
      sourceMap: true
    }),
  ],

  /**
   * Set the target environment to 'web'
   */
  target: 'web',

  /**
   * Do not watch by default, we do it via gulp
   */
  watch: false,
};
