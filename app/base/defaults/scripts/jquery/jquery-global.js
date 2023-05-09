import jQuery from './libs/jquery-fix';
import { start } from './libs/jquery-defer';

window.jQuery = jQuery;
window.$ = jQuery;

start();
