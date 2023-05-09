import { popup } from './libs/popup-old';

popup( '[href^="#popup-"]', {
	type: 'inline',
	midClick: true,
} );

popup(
	'[href^="https://www.youtube.com/watch?v="], [href^="https://vimeo.com/"]',
	{
		type: 'iframe',
		midClick: true,
		iframe: {
			patterns: {
				youtube: {
					index: 'youtube.com/',
					id: 'v=',
					src: '//www.youtube.com/embed/%id%?autoplay=1&rel=0',
				},
			},
		},
	}
);
