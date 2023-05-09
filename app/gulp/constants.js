import yargs from 'yargs';
import yaml from 'js-yaml';
import { readFileSync } from 'fs';
import { resolve } from 'path';

export function loadConfig() {
	const ymlFile = readFileSync( 'config.yml', 'utf8' );
	return yaml.load( ymlFile, {} );
}

export const { PORT, PATHS, WOOCOMMERCE_ENABLED, JQUERY_ENABLED } =
	loadConfig();
export const PRODUCTION = !! yargs.argv.production;
export const TARGET = process.env.npm_lifecycle_event;
export const IS_SERVER = TARGET === 'start';
export const BROWSER_OPEN = !! yargs.argv.open;
export const APP_PATH = resolve( __dirname, '../' );
export const APP_DIST_PATH = PATHS.appDist;
export const MARKUP_DIST_PATH = PATHS.markupDist;
export const ASSETS_PATH = PATHS.assets;
export const SCRIPT_PATHS = PATHS.scripts;
export const SCRIPT_MODULE_PATHS = PATHS.scriptsModules;
export const SCRIPT_LIB_PATHS = PATHS.scriptsLibs;
export const SCRIPT_NPM_PROXY_PATHS = PATHS.scriptsNPMProxy;
export const STYLE_PATHS = PATHS.styles;
export const FONTS_PATH = PATHS.fonts;
export const WORDPRESS_THEME_PATH = PATHS.WordPressTheme;
export const WOOCOMMERCE_PATHS = PATHS.WooCommerce;
export const WATCH_PAGES = PATHS.watch.pages;
export const WATCH_PAGES_DEPENDENCIES = PATHS.watch.pagesDependencies;
export const WATCH_SCRIPTS = PATHS.watch.scripts;
export const WATCH_STYLES = PATHS.watch.styles;
export const CACHE_DIR = resolve( APP_PATH, 'node_modules/.cache' );

/**
 * This is just a stub for the editor
 * so that it does not highlight the properties defined in YML.
 */
export const never = {
	appDist: '',
	markupDist: '',
	WordPressTheme: '',
	WooCommerce: {},
	scriptsModules: [],
	scriptsLibs: [],
};
