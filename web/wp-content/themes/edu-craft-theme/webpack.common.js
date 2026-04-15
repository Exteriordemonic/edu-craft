const path = require('path');
const glob = require('glob');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

const THEME_ROOT = __dirname;
const STYLE_ENTRY_GLOB = 'blocks/*/style.scss';
const EDITOR_STYLE_ENTRY_GLOB = 'blocks/*/editor.scss';

function toWebpackEntryKey(filePath, outputDir) {
	const blockName = path.basename(path.dirname(filePath));
	return `${outputDir}/${blockName}`;
}

function buildBlockEntries() {
	const entries = {};

	glob.sync(STYLE_ENTRY_GLOB, { cwd: THEME_ROOT }).forEach((file) => {
		entries[toWebpackEntryKey(file, 'css')] = path.resolve(THEME_ROOT, file);
	});

	glob.sync(EDITOR_STYLE_ENTRY_GLOB, { cwd: THEME_ROOT }).forEach((file) => {
		entries[`css/${path.basename(path.dirname(file))}-editor`] = path.resolve(THEME_ROOT, file);
	});

	return entries;
}

module.exports = {
	entry: {
		'js/main': path.resolve(THEME_ROOT, 'src/js/main.js'),
		'js/editor': path.resolve(THEME_ROOT, 'src/js/editor.js'),
		'js/woocommerce': path.resolve(THEME_ROOT, 'src/js/woocommerce.js'),
		'css/main': path.resolve(THEME_ROOT, 'src/scss/main.scss'),
		'css/editor': path.resolve(THEME_ROOT, 'src/scss/editor.scss'),
		...buildBlockEntries(),
	},
	output: {
		path: path.resolve(THEME_ROOT, 'dist'),
		filename: '[name].js',
	},
	module: {
		rules: [
			{
				test: /\.js$/,
				exclude: /node_modules/,
				use: [],
			},
			{
				test: /\.(sa|sc|c)ss$/,
				use: [
					MiniCssExtractPlugin.loader,
					'css-loader',
					'postcss-loader',
					'sass-loader',
				],
			},
		],
	},
	plugins: [
		new MiniCssExtractPlugin({
			filename: '[name].css',
		}),
	],
};
