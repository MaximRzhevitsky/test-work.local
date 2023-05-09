import { rimraf } from 'rimraf';
import { src, dest, series } from 'gulp';
import { APP_DIST_PATH, WORDPRESS_THEME_PATH } from '../constants';

export function wpCopyGenerator( folder ) {
	if ( typeof folder === 'undefined' ) {
		folder = '**';
	}

	const copyToWP = () => {
		return src( `${ APP_DIST_PATH }/assets/${ folder }/**/*` ).pipe(
			'**' === folder
				? dest( `${ WORDPRESS_THEME_PATH }/assets` )
				: dest( `${ WORDPRESS_THEME_PATH }/assets/${ folder }` )
		);
	};

	const clearFontFolderWP = async ( done ) => {
		await rimraf( `${ WORDPRESS_THEME_PATH }/assets/fonts` );
		done();
	};

	// noinspection UnnecessaryLocalVariableJS
	const wpCopy = ( cb ) => {
		if ( 'fonts' === folder ) {
			return series( clearFontFolderWP, copyToWP )( cb );
		}

		return series( copyToWP )( cb );
	};

	return wpCopy;
}
