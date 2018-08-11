/******************************************************************************
 * World's Best Webpack config for JS, VueJS and LessCSS.
 *
 * Use it by running NPM scripts:
 *  npm run watch -> "./node_modules/.bin/webpack --watch"
 *  npm run dev   -> "./node_modules/.bin/webpack --mode development"
 *  npm run prod  -> "./node_modules/.bin/webpack --mode production"
 *****************************************************************************/

const path = require("path");

const CleanWebpackPlugin = require("clean-webpack-plugin");
const CopyWebpackPlugin = require("copy-webpack-plugin");
const FriendlyErrorsWebpackPlugin = require("friendly-errors-webpack-plugin");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const VueLoaderPlugin = require("vue-loader/lib/plugin");
const WebpackBuildNotifierPlugin = require("webpack-build-notifier");

module.exports = function (env = {}, argv = {}) {
    const DEST_FOLDER = path.resolve(__dirname, "www/assets");
    const MODE = env.mode || argv.mode || "development";
    const IS_PROD = MODE === "production";

    return {
        entry: {
            app: "./assets/index.js",
        },

        output: {
            path: DEST_FOLDER,
            // You can actually put folders in front of the filename
            filename: "js/[name].bundle.js",
            // filename: "[name].[chunkhash].js"
        },

        mode: MODE,
        performance: { hints: false, },
        devtool: IS_PROD ? false : "source-map",

        optimization: {
            // splitChunks: {
            //     chunks: 'initial',
            //     name: 'lib',
            // },
        },

        module: {
            rules: [
                {
                    test: /\.js$/,
                    exclude: /node_modules/,
                    use: "babel-loader",
                },
                {
                    test: /\.vue$/,
                    loader: "vue-loader",
                },
                {
                    test: /\.less$/,
                    use: [
                        { // (4) Create actual CSS files, be done with it.
                            loader: MiniCssExtractPlugin.loader,
                            options: {
                                // Don't ask me about this, just
                                publicPath: "../",
                            }
                        },
                        { // (3) Now this one "translates CSS into CommonJS", whatever that means.
                            loader: "css-loader",
                            options: {
                                minimize: IS_PROD,
                            },
                        },
                        { // (2) This one would enable autoprefixer.
                            loader: "postcss-loader",
                            options: {
                                plugins: () => [require("autoprefixer")()],
                            },
                        },
                        { // (1) We start compiling LessCSS into regular CSS
                            loader: "less-loader",
                            options: {
                                strictMath: true,
                            },
                        },
                    ],
                },
                {
                    test: /\.(jpg|png|svg)$/,
                    loader: "url-loader",
                    options: {
                        name: "img/[name].[ext]",
                        // Files smaller than 4 KiB will be inlined as data URLs
                        limit: 4096,
                    },
                },
            ],
        },

        plugins: [
            // Wipe out the destination folder on compile.
            new CleanWebpackPlugin([DEST_FOLDER], {}),

            // Maybe you need to copy some files.
            new CopyWebpackPlugin([
                // {from: './assets/img/icons', to: 'img/icons'},
            ], {}),

            new FriendlyErrorsWebpackPlugin(),

            // CSS extraction -- without this plugin, the CSS will just be a string inside
            // the main.js file.
            new MiniCssExtractPlugin({
                // As with "output" above, you can put folders in front of the filename
                filename: "css/app.bundle.css",
            }),

            new VueLoaderPlugin(),

            new WebpackBuildNotifierPlugin({
                  title: "Duels assets",
                  // Will only show popups on errors and first success after an error.
                  suppressSuccess: true,
            }),

            // No idea what this is for. I think it adds a querystring parameter to
            // the URLs of <link:href> and <script:src> to version them. Since
            // the HtmlWebpackPlugin is disabled, this should effectively do nothing.
            // new WebpackMd5Hash(),
        ],
    };
};
