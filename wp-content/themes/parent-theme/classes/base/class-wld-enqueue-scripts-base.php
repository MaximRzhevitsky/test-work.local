<?php

class WLD_Enqueue_Scripts_Base extends WLD_Enqueue {
	public static function init() : void {
		parent::init();

		add_filter(
			'wp_enqueue_scripts',
			array( static::class, 'jquery_to_footer' )
		);
		add_filter(
			'wp_footer',
			array( static::class, 'jquery_top_footer' ),
			PHP_INT_MIN
		);
		add_filter(
			'gform_get_form_filter',
			array( static::class, 'remove_type_javascript_from_gf' )
		);
		if ( WLD_Is::woocommerce_enabled() ) {
			add_filter(
				'wp_footer',
				array( static::class, 'remove_type_javascript_from_woo' ),
				1
			);
		}
	}

	public static function enqueue_base() : void {
		$handle = static::enqueue_file( 'main.js' );
		if ( $handle ) {
			WLD_Defer_Scripts::add( $handle );
			wp_localize_script(
				$handle,
				'theme',
				apply_filters(
					'wld_enqueue_get_theme_object',
					array(
						'url'       => WLD_File::get_assets_url(),
						'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
						'ajaxNonce' => wp_create_nonce( 'ajax-nonce' ),
					)
				)
			);

			wp_localize_script(
				$handle,
				'theme_i18n',
				apply_filters(
					'wld_enqueue_get_theme_i18n_object',
					array(
						'more' => __( 'View More', 'theme' ),
						'less' => __( 'View Less', 'theme' ),
					)
				)
			);
		}

		if ( WLD_Is::woocommerce_enabled() ) {
			static::enqueue_file( 'woocommerce.js', array( 'deps' => 'jquery' ) );
			WLD_Defer_Scripts::add( 'theme-woocommerce' );
		}

		if ( WLD_Theme::never() ) { // The condition is never fulfilled, only for IDE
			?>
			<script>
				window.theme = { url: '', ajaxUrl: '', ajaxNonce: '' };
				window.theme_i18n = { more: '', less: '' };
			</script>
			<?php
		}
	}

	public static function jquery_to_footer() : void {
		$file    = 'js/jquery.js';
		$path    = WLD_File::get_assets_path( $file );
		$version = WLD_Enqueue_Scripts::get_version( $path );
		wp_deregister_script( 'jquery-core' );
		wp_deregister_script( 'jquery' );
		wp_register_script(
			'jquery-core',
			WLD_File::get_assets_url( $file ),
			array(),
			$version,
			true
		);
		wp_register_script(
			'jquery',
			false,
			array( 'jquery-core' ),
			$version,
			true
		);
	}

	public static function jquery_top_footer() : void {
		wp_scripts()->do_items( 'jquery-core' );
	}

	public static function remove_type_javascript_from_gf( string $form_string ) : string {
		return str_replace( ' type=\'text/javascript\'', '', $form_string );
	}

	public static function remove_type_javascript_from_woo() : void {
		remove_action( 'wp_footer', 'wc_no_js' );
		wp_add_inline_script(
			'jquery',
			/** @lang JavaScript */<<<'JS'
			( function () {
				let c = document.body.className;
				c = c.replace(/woocommerce-no-js/, 'woocommerce-js');
				document.body.className = c;
			} )();
JS,
			'before'
		);
	}
}

