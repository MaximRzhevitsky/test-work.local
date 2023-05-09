import { rimraf } from 'rimraf';
import gulpIf from 'gulp-if';
import { src, dest } from 'gulp';
import {
	ASSETS_PATH,
	APP_DIST_PATH,
	MARKUP_DIST_PATH,
	PRODUCTION,
	CACHE_DIR,
} from './constants';
import { existsSync, readFileSync, writeFileSync, mkdirSync } from 'fs';
import { resolve, sep } from 'path';

export async function clear( done ) {
	await rimraf( CACHE_DIR );
	await rimraf( APP_DIST_PATH );
	await rimraf( MARKUP_DIST_PATH );

	done();
}

export function copy() {
	return src( ASSETS_PATH )
		.pipe( dest( APP_DIST_PATH ) )
		.pipe( gulpIf( PRODUCTION, dest( MARKUP_DIST_PATH ) ) );
}

export function appDest( path = '', options = {} ) {
	return dest( `${ APP_DIST_PATH }/${ path }`, options );
}

export function wwwDest( path = '', options = {} ) {
	return gulpIf(
		PRODUCTION,
		dest( `${ MARKUP_DIST_PATH }/${ path }`, options )
	);
}

function getLastRunPath( cacheKey ) {
	return resolve(
		CACHE_DIR,
		'last-run',
		cacheKey + ( PRODUCTION ? '-PRD' : '-DEV' )
	);
}

export function getLastRun( cacheKey ) {
	const cachePath = getLastRunPath( cacheKey );
	if ( existsSync( cachePath ) ) {
		return parseInt( readFileSync( cachePath ).toString(), 10 );
	}

	setLastRun( cacheKey );

	return 0;
}

export function setLastRun( cacheKey ) {
	const cachePath = getLastRunPath( cacheKey );

	mkdirSync( cachePath.split( sep ).slice( 0, -1 ).join( sep ), {
		recursive: true,
	} );
	writeFileSync( cachePath, Date.now().toString() );
}
