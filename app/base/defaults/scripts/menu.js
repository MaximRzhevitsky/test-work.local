import liveDom from './libs/live-dom';
import accessibilityMenu from './libs/accessibility-menu';

liveDom( '[data-accessibility-menu]' ).firstShow( function () {
	accessibilityMenu( this );
} );
