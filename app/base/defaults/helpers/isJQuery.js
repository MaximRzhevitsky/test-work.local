import { JQUERY_ENABLED, WOOCOMMERCE_ENABLED } from '../../../gulp/constants';

module.exports = function () {
	return JQUERY_ENABLED || WOOCOMMERCE_ENABLED;
};
