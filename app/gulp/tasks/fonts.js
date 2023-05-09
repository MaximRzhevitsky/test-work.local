import localFonts from '@sumotto/gulp-local-fonts';
import { src } from 'gulp';
import { relative } from 'path';
import { minify as minifyCss } from 'csso';
import { minifySync as minifyJs } from 'terser-sync/lib';
import { appDest, getLastRun, wwwDest } from '../helpers';
import { Transform } from 'stream';
import {
	FONTS_PATH,
	WOOCOMMERCE_ENABLED,
	WOOCOMMERCE_PATHS,
} from '../constants';

function maybeAddWoocommerceFont() {
	const transformStream = new Transform( { objectMode: true } );

	transformStream._transform = function ( file, encoding, callback ) {
		const json = JSON.parse( file.contents.toString() );

		if ( WOOCOMMERCE_ENABLED && WOOCOMMERCE_PATHS.fonts ) {
			if ( typeof json.local === 'undefined' ) {
				json.local = [];
			}

			json.local.push( relative( file.base, WOOCOMMERCE_PATHS.fonts ) );
			file.contents = new Buffer.from( JSON.stringify( json ) );
		}

		callback( null, file );
	};

	return transformStream;
}

export function getFontPaths() {
	return FONTS_PATH;
}

export function fonts() {
	return src( getFontPaths(), {
		since: getLastRun( 'fonts' ),
	} )
		.pipe( maybeAddWoocommerceFont() )
		.pipe(
			localFonts( {
				cssTransform: ( { css } ) => minifyCss( css ).css,
				jsTransform: ( { js } ) =>
					minifyJs( js, { module: true } ).code,
			} )
		)
		.pipe( appDest( 'assets/fonts' ) )
		.pipe( wwwDest( 'assets/fonts' ) );
}
