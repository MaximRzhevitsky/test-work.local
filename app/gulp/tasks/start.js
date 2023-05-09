import browser from 'browser-sync';
import { series, watch } from 'gulp';
import {
	ASSETS_PATH,
	BROWSER_OPEN,
	APP_DIST_PATH,
	PORT,
	WATCH_SCRIPTS,
	WATCH_STYLES,
	WATCH_PAGES,
	WATCH_PAGES_DEPENDENCIES,
} from '../constants';
import { copy } from '../helpers';
import {
	getScriptLibPaths,
	getScriptModulePaths,
	getScriptNPMProxyPaths,
	getScriptPaths,
	libs,
	modules,
	npmProxy,
	scripts,
} from './scripts';
import { fonts, getFontPaths } from './fonts';
import { getStylePaths, styles } from './styles';
import { pages, resetPages } from './templates';
import { getImagePaths, images } from './images';

export function server( done ) {
	browser.init( {
		server: APP_DIST_PATH,
		port: PORT,
		open: BROWSER_OPEN,
		directory: true,
	} );
	done();
}

export function watchFiles() {
	watch( ASSETS_PATH, copy );
	watch( WATCH_PAGES ).on( 'all', series( pages, browser.reload ) );
	watch( WATCH_PAGES_DEPENDENCIES ).on(
		'all',
		series( resetPages, pages, browser.reload )
	);
	watch( [ ...getStylePaths(), ...WATCH_STYLES ] ).on(
		'all',
		series( styles, browser.stream )
	);
	watch( [ ...getScriptPaths(), ...WATCH_SCRIPTS ] ).on(
		'all',
		series( scripts, browser.reload )
	);
	watch( getScriptModulePaths() ).on(
		'all',
		series( modules, browser.reload )
	);
	watch( getScriptLibPaths() ).on( 'all', series( libs, browser.reload ) );
	watch( getScriptNPMProxyPaths( true ) ).on(
		'all',
		series( npmProxy, browser.reload )
	);
	watch( [
		...getImagePaths( 'base/defaults' ),
		...getImagePaths( 'src' ),
		...getImagePaths( 'base/woocommerce' ),
	] ).on( 'all', series( images, browser.reload ) );
	watch( getFontPaths() ).on( 'all', series( fonts, browser.reload ) );
}
