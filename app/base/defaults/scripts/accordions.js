import liveDom from './libs/live-dom';
import accessibilityAccordion from './libs/accessibility-accordion';

liveDom( '.accordion' ).firstShow( function () {
	accessibilityAccordion( this );
} );
