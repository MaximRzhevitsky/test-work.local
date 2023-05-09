import { slider } from '../../../base/defaults/scripts/libs/slider';

slider('.slider', {
	mobileFirst: true,
	autoplay: true,
	infinite: true,
	cssEase: 'ease-out',
	autoplaySpeed: 2000,
	speed: 1000,
	arrows: true,
	slidesToShow: 1,
	slidesToScroll: 1,
	responsive: [
		{
			breakpoint: 412,
			settings: {
				slidesToShow: 2,
			},
		},
		{
			breakpoint: 768,
			settings: {
				slidesToShow: 4,
			},
		},
		{
			breakpoint: 1024,
			settings: {
				slidesToShow: 6,
			},
		},
	],
});
