import { basename, resolve, relative, sep, dirname } from 'path';
import {
	statSync,
	mkdirSync,
	writeFileSync,
	existsSync,
	readFileSync,
} from 'fs';
import stylelint from 'stylelint';
import postcssImport from 'postcss-import';
import postcssExtendRule from 'postcss-extend-rule';
import postcssAdvancedVariables from 'postcss-advanced-variables';
import postcssCalc from 'postcss-calc';
import postcssInlineSvg from 'postcss-inline-svg';
import postcssColorModFunction from 'postcss-color-mod-function';
import postcssPresetEnv from 'postcss-preset-env';
import postcssImportExtGlob from 'postcss-import-ext-glob';
import postcssAtElse from 'postcss-at-else';
import postcssAtModifier from '../postcss-at-modifier';
import postcssNested from 'postcss-nested';
import postcssSortMediaQueries from 'postcss-sort-media-queries';
import postcssPxtorem from 'postcss-pxtorem';
import postcssSvgo from 'postcss-svgo';
import postcssUrl from 'postcss-url';
import postcssCsso from 'postcss-csso';
import postcssBrowserReporter from 'postcss-browser-reporter';
import postcssReporter from 'postcss-reporter';
import postcssScss from 'postcss-scss';
import gulpExtname from 'gulp-extname';
import gulpPostcss from 'gulp-postcss';
import gulpIf from 'gulp-if';
import browser from 'browser-sync';
import { src } from 'gulp';
import {
	APP_PATH,
	CACHE_DIR,
	PRODUCTION,
	STYLE_PATHS,
	WOOCOMMERCE_ENABLED,
	WOOCOMMERCE_PATHS,
} from '../constants';
import { appDest, wwwDest } from '../helpers';
import { globSync } from 'glob';
import flatStructure from '../flat-structure';

const postcssEnvOptions = {
	stage: false,
	features: {
		'all-property': false,
		'any-link-pseudo-class': false,
		'blank-pseudo-class': false,
		'break-properties': false,
		'case-insensitive-attributes': false,
		'color-functional-notation': false,
		'custom-media-queries': true,
		'custom-properties': true,
		'custom-selectors': true,
		'dir-pseudo-class': false,
		'double-position-gradients': false,
		'environment-variables': false,
		'focus-visible-pseudo-class': false,
		'focus-within-pseudo-class': false,
		'font-variant-property': false,
		'gap-properties': false,
		'has-pseudo-class': false,
		'hexadecimal-alpha-notation': false,
		'image-set-function': false,
		'is-pseudo-class': false,
		'lab-function': false,
		'logical-properties-and-values': false,
		'media-query-ranges': true,
		'nesting-rules': false,
		'not-pseudo-class': false,
		'overflow-property': false,
		'overflow-wrap-property': false,
		'place-properties': false,
		'prefers-color-scheme-query': false,
		'rebeccapurple-color': false,
		'system-ui-font-family': false,
	},
};

const postcssPxToRemOptions = {
	rootValue: 16,
	unitPrecision: 5,
	propList: [ 'font', 'font-size', 'line-height', 'letter-spacing' ],
	selectorBlackList: [],
	replace: true,
	mediaQuery: true,
	minPixelValue: 0,
	exclude: /node_modules/i,
};

const postcssOptions = {
	map: ! PRODUCTION,
	syntax: postcssScss,
};

const postcssUrlOptions = {
	url: ( asset ) => {
		return '../images/' + basename( asset.pathname );
	},
};

let postcssPlugins;

if ( PRODUCTION ) {
	postcssPlugins = [
		postcssImportExtGlob,
		postcssImport( {
			plugins: [ stylelint ],
		} ),
		postcssExtendRule,
		postcssAdvancedVariables,
		postcssCalc( { mediaQueries: true } ),
		postcssInlineSvg,
		postcssUrl( postcssUrlOptions ),
		postcssColorModFunction,
		postcssPresetEnv( postcssEnvOptions ),
		postcssAtModifier,
		postcssAtElse,
		postcssNested,
		postcssSortMediaQueries,
		postcssPxtorem( postcssPxToRemOptions ),
		postcssSvgo,
		postcssCsso,
		postcssBrowserReporter,
		postcssReporter( { clearReportedMessages: true } ),
	];
} else {
	postcssPlugins = [
		postcssImportExtGlob,
		postcssImport( {
			plugins: [ stylelint ],
		} ),
		postcssExtendRule,
		postcssAdvancedVariables,
		postcssCalc( { mediaQueries: true } ),
		postcssInlineSvg,
		postcssUrl( postcssUrlOptions ),
		postcssColorModFunction,
		postcssPresetEnv( postcssEnvOptions ),
		postcssAtModifier,
		postcssAtElse,
		postcssNested,
		postcssSortMediaQueries,
		postcssBrowserReporter,
		postcssReporter( { clearReportedMessages: true } ),
	];
}

export function getStylePaths() {
	const paths = [ ...STYLE_PATHS ];

	if ( WOOCOMMERCE_ENABLED && WOOCOMMERCE_PATHS.styles ) {
		paths.push( ...WOOCOMMERCE_PATHS.styles );
	}

	return paths;
}

function filterPaths( paths ) {
	const dependenciesInfo = {};

	function getFileCachePath( path ) {
		const cacheKey = relative( APP_PATH, path ).replaceAll( sep, '-' );

		return resolve(
			CACHE_DIR,
			'styles',
			cacheKey + ( PRODUCTION ? '-PRD' : '-DEV' )
		);
	}

	function getFileCacheInfo( path ) {
		const cachePath = getFileCachePath( path );
		if ( existsSync( cachePath ) ) {
			return JSON.parse( readFileSync( cachePath ).toString() );
		}

		return false;
	}

	function setDependenciesForPath( path ) {
		const dependencies = [];
		const content = readFileSync( path ).toString();
		const imports = content.match(
			/(?:^|;|{|}|\*\/)\s*@(import|import-glob)\s+((?:"[^"]+"|'[^']+')(?:\s*,\s*(?:"[^"]+"|'[^']+'))*)(?=[^;]*;)/gm
		);

		if ( imports ) {
			imports.forEach( function ( i ) {
				const importPaths = i.match( /(?<=['"])([^'"\s]+)(?=["'])/gm );

				if ( importPaths ) {
					importPaths.forEach( function ( p ) {
						const dependencyPath = resolve( dirname( path ), p );

						if (
							typeof dependenciesInfo[ dependencyPath ] ===
							'undefined'
						) {
							dependenciesInfo[ dependencyPath ] = {
								path: dependencyPath,
								mtime: Math.floor(
									new Date(
										statSync(
											dependencyPath
										).mtime.toString()
									).getTime() / 1000
								),
							};
						}

						if (
							typeof dependenciesInfo[ dependencyPath ]
								.dependencies === 'undefined'
						) {
							dependenciesInfo[ dependencyPath ].dependencies =
								[];
							dependenciesInfo[ dependencyPath ].dependencies =
								setDependenciesForPath( dependencyPath );
						}

						dependencies.push( dependenciesInfo[ dependencyPath ] );
						dependencies.push(
							...dependenciesInfo[ dependencyPath ].dependencies
						);
					} );
				}
			} );
		}

		return dependencies;
	}

	function setFileCacheInfo( path ) {
		const info = {
			mtime: Math.floor(
				new Date( statSync( path ).mtime.toString() ).getTime() / 1000
			),
			dependencies: setDependenciesForPath( path ),
		};
		const cachePath = getFileCachePath( path );

		mkdirSync( cachePath.split( sep ).slice( 0, -1 ).join( sep ), {
			recursive: true,
		} );

		writeFileSync( cachePath, JSON.stringify( info ) );
	}

	return paths.filter( function ( path ) {
		const info = getFileCacheInfo( path );
		if ( info ) {
			const mtime = Math.floor(
				new Date( statSync( path ).mtime.toString() ).getTime() / 1000
			);
			if ( mtime === info.mtime ) {
				if (
					info.dependencies.filter(
						( dependencyInfo ) =>
							Math.floor(
								new Date(
									statSync(
										dependencyInfo.path
									).mtime.toString()
								).getTime() / 1000
							) !== dependencyInfo.mtime
					).length === 0
				) {
					return false;
				}
			}
		}

		setFileCacheInfo( path );

		return true;
	} );
}

export function styles() {
	const paths = filterPaths( globSync( getStylePaths() ) );

	return src( paths.length ? paths : 'empty-glob', {
		allowEmpty: true,
	} )
		.pipe( gulpPostcss( postcssPlugins, postcssOptions ) )
		.pipe( gulpExtname( '.css' ) )
		.pipe( flatStructure() )
		.pipe( appDest( 'assets/css' ) )
		.pipe( gulpIf( ! PRODUCTION, browser.stream() ) )
		.pipe( wwwDest( 'assets/css' ) );
}
