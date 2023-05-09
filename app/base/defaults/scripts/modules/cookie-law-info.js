/* eslint-disable import/extensions */
import addStyle from './add-style.js';

const path = '/wp-content/plugins/cookie-law-info/public/css/cookie-law-info-';

Promise.all([
	addStyle(`${path}public.css`),
	addStyle(`${path}gdpr.css`),
	addStyle(`${path}table.css`),
]).then(() => {
	document.documentElement.classList.add('cookie-law-info-loaded');
	document.documentElement.classList.remove('cookie-law-info-hidden');
});
