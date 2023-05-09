import { parallel, src } from 'gulp';
import gulpIndex from 'gulp-index';
import gulpExtname from 'gulp-extname';
import { appDest, getLastRun, setLastRun, wwwDest } from '../helpers';
import {
	paniniDefaults,
	paniniDefaultsRefresh,
} from '../panini/panini-defaults';
import { paniniProject, paniniProjectRefresh } from '../panini/panini-project';
import {
	paniniWoocommerce,
	paniniWoocommerceRefresh,
} from '../panini/panini-woocommerce';
import {
	APP_PATH,
	CACHE_DIR,
	MARKUP_DIST_PATH,
	PRODUCTION,
	WATCH_PAGES_DEPENDENCIES,
	WOOCOMMERCE_ENABLED,
} from '../constants';
import htmlModification from '../html-modification';
import { relative, resolve, sep } from 'path';
import { globSync } from 'glob';
import globDebug from 'gulp-debug';
import {
	existsSync,
	mkdirSync,
	readFileSync,
	statSync,
	writeFileSync,
} from 'fs';

export function paniniOptions( root ) {
	return {
		root,
		layouts: [
			'base/woocommerce/layouts/',
			'base/defaults/layouts/',
			'src/layouts/',
		],
		partials: [
			'base/woocommerce/partials/',
			'base/defaults/partials/',
			'src/partials/',
		],
		data: 'src/data/',
		helpers: [
			'base/woocommerce/helpers/',
			'base/defaults/helpers/',
			'src/helpers/',
		],
	};
}

function getFileCachePath( path ) {
	const cacheKey = relative( APP_PATH, path ).replaceAll( sep, '-' );

	return resolve(
		CACHE_DIR,
		'templates',
		cacheKey + ( PRODUCTION ? '-PRD' : '-DEV' ) + '.json'
	);
}

function getFileCacheInfo( path ) {
	const cachePath = getFileCachePath( path );
	if ( existsSync( cachePath ) ) {
		return JSON.parse( readFileSync( cachePath ).toString() );
	}

	return false;
}

function checkModifiedDependencies( cacheKey ) {
	let dependenciesInfo = getFileCacheInfo(
		APP_PATH + '/' + cacheKey + '.dependencies'
	);

	if ( dependenciesInfo ) {
		if (
			dependenciesInfo.filter(
				( dependencyInfo ) =>
					Math.floor(
						new Date(
							statSync( dependencyInfo.path ).mtime.toString()
						).getTime() / 1000
					) !== dependencyInfo.mtime
			).length === 0
		) {
			return getLastRun( cacheKey );
		}
	}

	dependenciesInfo = [];

	const dependencyPaths = globSync( WATCH_PAGES_DEPENDENCIES );
	dependencyPaths.forEach( function ( dependencyPath ) {
		dependenciesInfo.push( {
			path: dependencyPath,
			mtime: Math.floor(
				new Date(
					statSync( dependencyPath ).mtime.toString()
				).getTime() / 1000
			),
		} );
	} );

	const cachePath = getFileCachePath(
		APP_PATH + '/' + cacheKey + '.dependencies'
	);

	mkdirSync( cachePath.split( sep ).slice( 0, -1 ).join( sep ), {
		recursive: true,
	} );

	writeFileSync( cachePath, JSON.stringify( dependenciesInfo ) );

	setLastRun( cacheKey );

	return 0;
}

export function pagesDefaults() {
	return src( 'base/defaults/pages/**/*.hbs', {
		since: checkModifiedDependencies( 'pagesDefaults' ),
	} )
		.pipe( paniniDefaults( paniniOptions( 'base/defaults/' ) ) )
		.pipe( gulpExtname() )
		.pipe( htmlModification() )
		.pipe( appDest( 'defaults' ) )
		.pipe( wwwDest( 'defaults' ) );
}

export function pagesProject() {
	return src( 'src/pages/**/*.hbs', {
		since: checkModifiedDependencies( 'pagesProject' ),
	} )
		.pipe( paniniProject( paniniOptions( 'src/pages/' ) ) )
		.pipe( gulpExtname() )
		.pipe( htmlModification() )
		.pipe( appDest() )
		.pipe( wwwDest() );
}

export function pagesWoocommerce() {
	return src( 'base/woocommerce/pages/**/*.hbs', {
		since: checkModifiedDependencies( 'pagesWoocommerce' ),
	} )
		.pipe( paniniWoocommerce( paniniOptions( 'base/woocommerce/' ) ) )
		.pipe( gulpExtname() )
		.pipe( htmlModification() )
		.pipe( appDest( 'woocommerce' ) )
		.pipe( wwwDest( 'woocommerce' ) );
}

export function resetPages( done ) {
	paniniDefaultsRefresh();
	paniniProjectRefresh();
	if ( WOOCOMMERCE_ENABLED ) {
		paniniWoocommerceRefresh();
	}
	done();
}

export function addIndex() {
	return src( `${ MARKUP_DIST_PATH }/**/*.html` )
		.pipe(
			gulpIndex( {
				relativePath: '../www/',
				'title-template': () => '',
				'item-template': ( filepath, filename ) => {
					let className = 'index__item';
					if ( filepath ) {
						className += ' sub-dir';
					} else {
						className += ' root-dir';
					}

					if ( 'index.html' === filename ) {
						className += ' index';
					}

					let href;
					if ( filepath ) {
						href = filepath + '/' + filename;
					} else {
						href = filename;
					}

					return `
						<li class="${ className }">
							<a
								class="index__item-link"
								href="${ href }">
								${ filename }
							</a>
						</li>
					`;
				},
				'append-to-output': () => `
					<script>
						const rootItems = document.querySelectorAll('.root-dir');
						const rootSection = document.createElement('section');
						const rootTitle = document.createElement('h2');
						const rootList = document.createElement('ul');
						const incorrectTitles = document.querySelectorAll('ul h2');

						rootTitle.innerText = 'project';

						rootSection.appendChild(rootTitle);
						rootSection.appendChild(rootList);

						rootItems.forEach(function(rootItem) {
							rootItem.closest('section').style.display = 'none';

							if (!rootItem.classList.contains('index')) {
								rootList.appendChild(rootItem);
							}
						});

						incorrectTitles.forEach(function(incorrectTitle) {
							incorrectTitle
								.closest('section')
								.insertBefore(incorrectTitle, incorrectTitle.closest('ul'));
						});

						document.body.insertBefore(rootSection, document.body.querySelector('section'));
					</script>
					</body>
				`,
			} )
		)
		.pipe( wwwDest() );
}

export function pages( done ) {
	if ( WOOCOMMERCE_ENABLED ) {
		return parallel(
			pagesDefaults,
			pagesWoocommerce,
			pagesProject
		)( done );
	}

	return parallel( pagesDefaults, pagesProject )( done );
}
