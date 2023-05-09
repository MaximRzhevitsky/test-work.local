import { popup } from '../../base/defaults/scripts/libs/popup';

popup( '[href^="#popup-"], [data-popup-id]', {
	type: 'inline',
	modal: true,
} );

popup(
	'[href^="https://www.youtube.com/watch?v="], [href^="https://vimeo.com/video/"]',
	{
		type: 'iframe',
		modal: true,
	}
);
