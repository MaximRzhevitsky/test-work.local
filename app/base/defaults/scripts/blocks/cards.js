import accessibilityCard from '../libs/accessibility-card';
import liveDom from '../libs/live-dom';

liveDom('.accessibility-card').firstShow(function () {
	accessibilityCard(this);
});
