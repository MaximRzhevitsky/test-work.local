import path from 'path';
import { IS_SERVER, PRODUCTION, WORDPRESS_THEME_PATH } from './constants';

const ROOT = IS_SERVER ? '' : WORDPRESS_THEME_PATH.replace( /^\.\./, '' );

export default {
	mode: PRODUCTION ? 'production' : 'development',
	output: {
		publicPath: path.normalize( `${ ROOT }/assets/js/` ),
	},
	module: {
		rules: [
			{
				test: /.js$/,
				use: [
					{
						loader: 'babel-loader',
					},
				],
			},
			{
				test: /\.css$/i,
				use: [ 'style-loader', 'css-loader' ],
			},
			{
				test: /\.s[ac]ss$/i,
				use: [ 'style-loader', 'css-loader', 'sass-loader' ],
			},
			{
				test: /\.png/i,
				use: [ 'url-loader' ],
			},
		],
	},
	externals: [
		function ( { context, request }, callback ) {
			if ( 'jquery' === request && /node_modules/.test( context ) ) {
				return callback( null, 'jQuery' );
			}

			callback();
		},
	],
	cache: {
		type: 'filesystem', // unfortunately the cache does not work :(
	},
	stats: 'errors-only',
};
