<?php

class WLD_Site_Map_Base {
	public static function init() : void {
		add_filter(
			'display_post_states',
			array( static::class, 'post_states' ),
			10,
			2
		);

		add_shortcode( 'site_map', array( static::class, 'site_map_shortcode' ) );

		WLD_Nav::add( 'Site Map' );
	}

	public static function post_states( array $post_states, WP_Post $post ) : array {
		if ( str_contains( $post->post_content, '[site_map]' ) ) {
			$post_states['site-map'] = __( 'Site Map Page', 'theme' );
		}

		return $post_states;
	}

	public static function site_map_shortcode() : string {
		return (string) wp_nav_menu(
			array(
				'theme_location' => WLD_Nav::get_location( 'Site Map' ),
				'container'      => false,
				'menu_class'     => 'menu-site_map',
				'echo'           => false,
			)
		);
	}
}
