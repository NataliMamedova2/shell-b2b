const { resolve } = require("path");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
// const { CheckerPlugin } = require("awesome-typescript-loader");
const Dotenv = require("dotenv-webpack");

module.exports = {
	resolve: {
		extensions: [".ts", ".tsx", ".js", ".jsx", ".css", ".scss"]
	},
	context: resolve(__dirname, "./source"),
	stats: {
		children: false,
		warnings: false
	},
	entry: {
		app: ["./index.scss", "./index.tsx"],
		utils: ["./utils.ts"],
		old: ["./old.scss"]
	},
	output: {
		filename: "./scripts/[name].js",
		path: resolve(__dirname + "/public"),
	},
	module: {
		rules: [
			{
				enforce: "pre",
				test: /\.(ts|tsx)$/,
				exclude: "/node_modules/",
				loader: "eslint-loader",
			},
			// {
			// 	test: /\.(ts|tsx)$/,
			// 	use: "awesome-typescript-loader"
			// },
			{
				test: /\.(ts|tsx)$/, loader: "ts-loader"
			},
			{
				enforce: "pre",
				test: /\.js$/,
				loader: "source-map-loader"
			},
			{
				test: /\.(scss|css)$/,
				use: [
					MiniCssExtractPlugin.loader,
					"css-loader?url=false",
					"postcss-loader",
					"sass-loader"
				]
			},
			{
				test: /\.(png|jpg|webp|gif|svg)$/,
				use: [
					{
						loader: "file-loader",
						options: {
							outputPath: "media"
						},
					},
				],
			}
		]
	},
	plugins: [
		new MiniCssExtractPlugin({
			filename: "./style/[name].css"
		}),
		// new CheckerPlugin(),
		new Dotenv({
			path: "./.env.local",
		})
	]
};
