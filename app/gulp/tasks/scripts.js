import { src } from 'gulp';
import { dirname } from 'path';
import named from 'vinyl-named';
import gulpTerser from 'gulp-terser';
import webpackStream from 'webpack-stream';
import webpackConfig from '../webpack.conf';
import { appDest, getLastRun, wwwDest } from '../helpers';
import {
	SCRIPT_PATHS,
	SCRIPT_MODULE_PATHS,
	WOOCOMMERCE_ENABLED,
	WOOCOMMERCE_PATHS,
	SCRIPT_LIB_PATHS,
	SCRIPT_NPM_PROXY_PATHS,
} from '../constants';

export function getScriptPaths() {
	const paths = [];

	paths.push( ...SCRIPT_PATHS );

	if ( WOOCOMMERCE_ENABLED && WOOCOMMERCE_PATHS.scripts ) {
		paths.push( ...WOOCOMMERCE_PATHS.scripts );
	}

	return paths;
}

export function getScriptModulePaths() {
	return SCRIPT_MODULE_PATHS;
}

export function getScriptLibPaths() {
	return SCRIPT_LIB_PATHS;
}

export function getScriptNPMProxyPaths( allFilesInFolder = false ) {
	if ( allFilesInFolder ) {
		return SCRIPT_NPM_PROXY_PATHS.map( ( path ) => {
			return dirname( path ) + '/**/*';
		} );
	}

	return SCRIPT_NPM_PROXY_PATHS;
}

export function scripts() {
	return src( getScriptPaths() )
		.pipe( named() )
		.pipe( webpackStream( webpackConfig ) )
		.pipe( appDest( 'assets/js' ) )
		.pipe( wwwDest( 'assets/js' ) );
}

export function modules() {
	return src( getScriptModulePaths(), {
		since: getLastRun( 'scriptsModules' ),
	} )
		.pipe( gulpTerser( { module: true } ) )
		.pipe( appDest( 'assets/js/modules' ) )
		.pipe( wwwDest( 'assets/js/modules' ) );
}

export function libs() {
	return src( getScriptLibPaths(), {
		since: getLastRun( 'scriptsLibs' ),
	} )
		.pipe( gulpTerser( { module: true } ) )
		.pipe( appDest( 'assets/js/libs' ) )
		.pipe( wwwDest( 'assets/js/libs' ) );
}

export function npmProxy() {
	return src( getScriptNPMProxyPaths() )
		.pipe( named() )
		.pipe(
			webpackStream( {
				...webpackConfig,
				output: {
					...webpackConfig.output,
					module: true,
					library: {
						type: 'module',
					},
				},
				experiments: {
					outputModule: true,
				},
			} )
		)
		.pipe( appDest( 'assets/js/npm-proxy' ) )
		.pipe( wwwDest( 'assets/js/npm-proxy' ) );
}
