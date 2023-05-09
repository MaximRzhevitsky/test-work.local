import { series, parallel } from 'gulp';
import { fonts } from './tasks/fonts';
import { scripts, modules, libs, npmProxy } from './tasks/scripts';
import { styles } from './tasks/styles';
import { clear, copy } from './helpers';
import { wpCopyGenerator } from './tasks/wordpress';
import { server, watchFiles } from './tasks/start';
import { addIndex, pages } from './tasks/templates';
import { build } from './tasks/build';
import { images } from './tasks/images';

exports.start = series( build, server, watchFiles );
exports.build = series( build, addIndex );
exports.clear = clear;
exports.templates = series( pages, addIndex, copy );
exports.wpBuild = series( build, addIndex, wpCopyGenerator() );
exports.wpStyles = series( styles, wpCopyGenerator( 'css' ) );
exports.wpFonts = series( fonts, wpCopyGenerator( 'fonts' ) );
exports.wpImages = series( images, wpCopyGenerator( 'images' ) );
exports.wpScripts = series(
	parallel( scripts, modules, libs, npmProxy ),
	wpCopyGenerator( 'js' )
);
