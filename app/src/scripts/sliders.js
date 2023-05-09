import { tinySlider } from '../../base/defaults/scripts/libs/tiny-slider';

tinySlider( '.slider', {
	items: 1,
	gutter: 30,
	autoplay: true,
	autoplayTimeout: 3000,
	autoplayButtonOutput: false,
	speed: 1500,
	controls: true,
	controlsText: [ '<span>&#10229;</span>', '<span>&#10230;</span>' ],
	navPosition: 'bottom',
	swipeAngle: false,
	mode: 'gallery',
	responsive: {
		350: {
			items: 2,
		},
		568: {
			items: 3,
		},
	},
} );

tinySlider( '.related.products .products', {
	items: 1,
	autoplay: true,
	autoplayTimeout: 5000,
	autoplayButtonOutput: false,
	speed: 1500,
	controls: false,
	navPosition: 'bottom',
	navAsThumbnails: true,
	swipeAngle: false,
	responsive: {
		412: {
			items: 2,
		},
		568: {
			items: 3,
		},
		991: {
			items: 4,
		},
		1200: {
			items: 5,
		},
	},
} );

tinySlider( '.tiny-slider-example', {
	items: 1,
	gutter: 30,
	autoplay: true,
	autoplayTimeout: 3000,
	autoplayButtonOutput: false,
	speed: 1500,
	controls: true,
	controlsText: [ '<span>&#10229;</span>', '<span>&#10230;</span>' ],
	navAsThumbnails: true,
	navPosition: 'bottom',
	swipeAngle: false,
	// mode: 'gallery',
	responsive: {
		350: {
			items: 2,
		},
		568: {
			items: 3,
		},
	},
} );
