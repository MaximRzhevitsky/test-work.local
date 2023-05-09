<?php

class WLD_Dequeue_Base {
	public static function init() : void {
		add_action(
			'wp_enqueue_scripts',
			array( static::class, 'remove_gutenberg_styles' ),
			999
		);
		add_action(
			'wp_enqueue_scripts',
			array( static::class, 'remove_wp_embed' )
		);
		add_action(
			'wp_default_scripts',
			array( static::class, 'remove_jquery_migrate' )
		);

		static::remove_gutenberg_assets();
	}

	public static function remove_gutenberg_styles() : void {
		wp_dequeue_style( 'wp-block-library' );
		wp_dequeue_style( 'wc-blocks-style' );
	}

	public static function remove_gutenberg_assets() : void {
		remove_action( 'wp_enqueue_scripts', 'wp_common_block_scripts_and_styles' );
		remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
		remove_action( 'wp_enqueue_scripts', 'wp_enqueue_classic_theme_styles' );
		remove_action( 'wp_footer', 'wp_enqueue_global_styles', 1 );
		remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );
		remove_action( 'wp_body_open', 'gutenberg_experimental_global_styles_render_svg_filters' );
	}

	public static function remove_wp_embed() : void {
		wp_deregister_script( 'wp-embed' );
	}

	public static function remove_jquery_migrate( $scripts ) : void {
		if ( isset( $scripts->registered['jquery'] ) && ! is_admin() ) {
			$script = $scripts->registered['jquery'];
			if ( $script->deps ) {
				$script->deps = array_diff( $script->deps, array( 'jquery-migrate' ) );
			}
		}
	}
}
