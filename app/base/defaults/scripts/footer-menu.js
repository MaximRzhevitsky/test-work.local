import liveDom from './libs/live-dom';
import navigationMenu from './libs/menu-navigation';

liveDom( '[data-menu-navigation]' ).firstShow( function () {
	navigationMenu( this );
} );
