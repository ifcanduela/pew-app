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
    const PROJECT_NAME = path.basename(__dirname);

    return {
        entry: {
            app: "./assets/index.js",
        },

        output: {
            path: DEST_FOLDER,
            filename: "js/[name].bundle.js",
        },

        mode: MODE,
        performance: { hints: false, },
        devtool: MODE === "production" ? false : "eval-source-map",
        stats: "none",

        optimization: {
            // splitChunks: {
            //     chunks: "initial",
            //     name: "lib",
            // },
        },

        resolve: {
            alias: {
                "vue$": "vue/dist/vue.esm.js",
            },
        },

        module: {
            rules: [
                {
                    test: /\.js$/,
                    loader: "babel-loader",
                    exclude: [/node_modules/],
                    options: {
                        presets: ["@babel/preset-env"],
                    },
                },
                {
                    test: /\.vue$/,
                    loader: "vue-loader",
                },
                {
                    test: /\.(c|le)ss$/,
                    use: [
                        {
                            loader: MiniCssExtractPlugin.loader,
                            options: {
                                publicPath: "../",
                            },
                        },
                        {
                            loader: "css-loader",
                        },
                        {
                            loader: "postcss-loader",
                            options: {
                                plugins: () => [
                                    require("autoprefixer")(),
                                    MODE === "production" ? require("cssnano")() : null,
                                ].filter(p => p !== null),
                            },
                        },
                        {
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
                {
                    test: /\.(ttf|woff2?|eot|otf)$/i,
                    loader: "url-loader",
                    options: {
                        limit: 8192,
                        fallback: "file-loader",
                        name: "fonts/[name].[ext]",
                    },
                },
            ],
        },

        plugins: [
            new CleanWebpackPlugin([DEST_FOLDER], {}),

            new CopyWebpackPlugin([
                // {from: "./assets/img/icons", to: "img/icons
            ], {}),

            new FriendlyErrorsWebpackPlugin(),

            new ImageminPlugin({
                test: /\.(jpe?g|png|gif|svg)$/i,
                disable: MODE !== "production",
            }),

            new MiniCssExtractPlugin({
                filename: "css/app.bundle.css",
            }),

            new VueLoaderPlugin(),

            new WebpackBuildNotifierPlugin({
                title: `${PROJECT_NAME} assets`,
                suppressSuccess: true,
            }),
        ],
    };
};
