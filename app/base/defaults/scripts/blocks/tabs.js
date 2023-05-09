import liveDom from '../libs/live-dom';
import accessibilityTabs from '../libs/accessibility-tabs';

liveDom('.tabs').firstShow(function () {
	accessibilityTabs(this);
});
