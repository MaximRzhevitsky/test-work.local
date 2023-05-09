<?php

if ( ! class_exists( 'WLD_Theme' ) ) {
	locate_template( 'classes/base/class-wld-theme-base.php', true );
	locate_template( 'classes/class-wld-theme.php', true );
	WLD_Theme::init();
}
add_filter(
	'woocommerce_single_product_image_thumbnail_nav_html',
	static function ( $html ) {
		$html = wp_kses(
			$html,
			array(
				'a'   => array(
					'href' => true,
				),
				'img' => array(
					'width'                   => true,
					'height'                  => true,
					'src'                     => true,
					'class'                   => true,
					'alt'                     => true,
					'decoding'                => true,
					'loading'                 => true,
					'title'                   => true,
					'data-caption'            => true,
					'data-src'                => true,
					'data-large_image'        => true,
					'data-large_image_width'  => true,
					'data-large_image_height' => true,
					'srcset'                  => true,
					'sizes'                   => true,
				),
			),
		);
		return $html;
	},
);
