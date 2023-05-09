<?php
// Scripts
if ( WLD_Is::woocommerce_enabled() ) {
	WLD_Defer_Scripts::add( 'jquery-blockui' );
	WLD_Defer_Scripts::add( 'wc-add-to-cart' );
	WLD_Defer_Scripts::add( 'js-cookie' );
	WLD_Defer_Scripts::add( 'woocommerce' );
	WLD_Defer_Scripts::add( 'wc-cart-fragments' );
} else {
	WLD_Defer_Scripts::add( 'jquery-core', static fn() => WLD_Is::frontend() && ! is_admin_bar_showing() );
}

// Styles
WLD_Enqueue_Styles::enqueue(
	array(
		'site-map'  => array(
			'site-map.css',
			'menu-site-map.css',
		),
		'faq'       => array(
			'faq.css',
			'blocks.css',
		),
		'thank-you' => array(
			'thank-you.css',
			'social-links.css'
		),
		'404'       => array(
			'error-404.css'
		),
        'banner'  => array(
            'banner.css'
        ),
        'why-we-do-this'  => array(
            'why-we-do-this.css'
        )
	),
	static function ( $css ) {
		return $css;
	}
);
