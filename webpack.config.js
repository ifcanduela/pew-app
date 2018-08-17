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
const ImageminPlugin = require("imagemin-webpack-plugin").default;
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const VueLoaderPlugin = require("vue-loader/lib/plugin");
const WebpackBuildNotifierPlugin = require("webpack-build-notifier");

module.exports = function (env = {}, argv = {}) {
    const DEST_FOLDER = path.resolve(__dirname, "www/assets");
    const MODE = env.mode || argv.mode || "development";

    return {
        entry: {
            app: "./assets/index.js",
        },

        output: {
            path: DEST_FOLDER,
            // You can actually put folders in front of the filename
            filename: "js/[name].bundle.js",
        },

        mode: MODE,
        performance: { hints: false, },
        devtool: MODE === "production" ? false : "eval-source-map",

        optimization: {
            // splitChunks: {
            //     chunks: "initial",
            //     name: "lib",
            // },
        },

        module: {
            rules: [
                {
                    test: /\.js$/,
                    loader: "babel-loader",
                    exclude: [/node_modules/],
                    options: {
                        presets: ["env"],
                    },
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
                                // Don't ask me about this, let it be.
                                publicPath: "../",
                            }
                        },
                        { // (3) Now this one "translates CSS into CommonJS", whatever that means.
                            loader: "css-loader",
                        },
                        { // (2) This one would enable autoprefixer and minification.
                            loader: "postcss-loader",
                            options: {
                                plugins: () => [
                                    require("autoprefixer")(),
                                    MODE === "production" ? require("cssnano")() : null,
                                ].filter(p => p !== null),
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
                    test: /\.(jpe?g|png|gif|svg)$/i,
                    loader: "url-loader",
                    options: {
                        limit: 8192,
                        fallback: "file-loader",
                        name: "img/[name].[ext]",
                    },
                },
            ],
        },

        plugins: [
            // Wipe out the destination folder on compile.
            new CleanWebpackPlugin([DEST_FOLDER], {}),

            // Maybe you need to copy some files.
            new CopyWebpackPlugin([
                // {from: "./assets/img/icons", to: "img/icons"},
            ], {}),

            new FriendlyErrorsWebpackPlugin(),

            // Image optimization
            new ImageminPlugin({
                test: /\.(jpe?g|png|gif|svg)$/i,
                disable: MODE !== "production",
            }),

            // CSS extraction -- without this plugin, the CSS will just be a string inside
            // the main.js file.
            new MiniCssExtractPlugin({
                // As with "output" above, you can put folders in front of the filename
                filename: "css/app.bundle.css",
            }),

            new VueLoaderPlugin(),

            new WebpackBuildNotifierPlugin({
                title: "Pew assets",
                // Will only show popups on errors and first success after an error.
                suppressSuccess: true,
            }),
        ],
    };
};
