import { parallel, series, src } from 'gulp';
import { appDest, getLastRun, wwwDest } from '../helpers';
import { WOOCOMMERCE_ENABLED } from '../constants';

export function getImagePaths( prefix ) {
	return [
		`${ prefix }/images/**/*`,
		`!${ prefix }/images/svg-load/*`,
		`!${ prefix }/images/svg-load`,
	];
}

export function imagesDefaults() {
	return src( getImagePaths( 'base/defaults' ), {
		since: getLastRun( 'imagesDefaults' ),
	} )
		.pipe( appDest( 'assets/images' ) )
		.pipe( wwwDest( 'assets/images' ) );
}

export function imagesProject() {
	return src( getImagePaths( 'src' ), {
		since: getLastRun( 'imagesProject' ),
	} )
		.pipe( appDest( 'assets/images' ) )
		.pipe( wwwDest( 'assets/images' ) );
}

export function imagesWoocommerce() {
	return src( getImagePaths( 'base/woocommerce' ), {
		since: getLastRun( 'imagesWoocommerce' ),
	} )
		.pipe( appDest( 'assets/images' ) )
		.pipe( wwwDest( 'assets/images' ) );
}

export function images( cb ) {
	if ( WOOCOMMERCE_ENABLED ) {
		return series(
			parallel( imagesDefaults, imagesWoocommerce ),
			imagesProject
		)( cb );
	}

	return series( imagesDefaults, imagesProject )( cb );
}
