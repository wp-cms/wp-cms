const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const cssnano = require('cssnano');
const autoprefixer = require('autoprefixer');
const TerserPlugin = require("terser-webpack-plugin");

module.exports = (env, argv) => {
  let config = {
    entry: {

      // Global CSS styles
      style: './src/scss.js',

      // Common JS used across the Site
      globalScripts: './src/js/global-scripts.js',

    },
    watch: true,
    watchOptions: {
      ignored: /node_modules/
    },
    output: {
      filename: '[name].js',
      chunkFilename: '[name].js?ver=[chunkhash]',
      publicPath: '/wp-content/themes/starter-theme/dist/',
    },
    resolve: {
      extensions: ['*', '.js'],
    },
    mode: 'development',
    performance: {
      hints: false,
    },
    devtool: 'source-map',
    module: {
      rules: [
        {
          test: /\.js$/,
          use: [
            {
              loader: 'babel-loader',
              options: {
                presets: ['@babel/preset-env']
              },
            },
          ],
        },
        {
          test: /\.(woff|woff2|eot|ttf|otf)$/,
          type: 'asset/resource',
        },
        {
          test: /\.(png|svg|jpg|jpeg|tiff|webp|gif|ico|mp4|webm|wav|mp3|m4a|aac|oga)$/,
          type: 'asset/resource',
        }
      ],
    },
    plugins: [
      new MiniCssExtractPlugin({
        filename: '[name].css',
      }),
    ],
  };

  if (argv.mode !== 'production') {
    config.module.rules.push({
      test: /\.s?css$/,
      use: [
        MiniCssExtractPlugin.loader,
        {
          loader: 'css-loader',
          options: {
            sourceMap: true,
          },
        },
        {
          loader: 'postcss-loader',
          options: {
            ident: 'postcss',
            plugins: [autoprefixer({})],
            sourceMap: true,
          },
        },
        {
          loader: 'sass-loader',
          options: {
            sourceMap: true,
            implementation: require("sass"),
            sassOptions: {
              precision: 10
            }
          },
        },
      ],
    });
  }

  if (argv.mode === 'production') {
    config.watch = false;
    config.module.rules.push({
      test: /\.s?css$/,
      use: [
        MiniCssExtractPlugin.loader,
        {
          loader: 'css-loader',
          options: {
            sourceMap: true,
          },
        },
        {
          loader: 'postcss-loader',
          options: {
            ident: 'postcss',
            plugins: [
              cssnano({
                preset: 'default',
              }),
              autoprefixer({}),
            ],
            sourceMap: true,
          },
        },
        {
          loader: 'sass-loader',
          options: {
            sourceMap: true,
            implementation: require("sass"),
            sassOptions: {
              precision: 10
            }
          },
        },
      ],
    });

    config.module.rules.push({
      test: /\.svg$/,
      enforce: 'pre',
      use: [
        {
          loader: 'svgo-loader',
          options: {
            precision: 2,
            plugins: [
              {
                removeViewBox: false,
              },
            ],
          },
        },
      ],
    });

    config.optimization = {
      minimize: true,
      minimizer: [new TerserPlugin()],
    };
  }

  return config;
};
