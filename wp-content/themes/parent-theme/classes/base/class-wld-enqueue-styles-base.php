<?php

class WLD_Enqueue_Styles_Base extends WLD_Enqueue {
	protected static ?Closure $callback = null;

	protected static array $blocks = array();

	public static function init() : void {
		parent::init();

		add_action(
			'wp_head',
			array( static::class, 'the_styles' ),
			0
		);
	}

	public static function enqueue_base() : void {
		$is_search = is_search();
		if ( $is_search || ! WLD_Is::flex_page() ) {
			static::enqueue_file( 'blog-and-defaults-pages.css' );
			if ( $is_search ) {
				static::enqueue_file( 'search-result.css' );
			}
		} elseif ( WLD_Is::woocommerce_page() ) {
			static::enqueue_file( 'woocommerce-pages.css' );
		}
	}

	public static function the_styles() : void {
		$css = static::get_css( 'main.css' );
		if ( WLD_Is::woocommerce_enabled() ) {
			$css .= static::get_css( 'woocommerce-main.css' );
			$css .= static::get_css( 'woocommerce-pages.css' );
		}
		if ( WLD_Is::breadcrumb_enabled() ) {
			$css .= static::get_css( 'breadcrumb.css' );
		}

		if ( WLD_Is::flex_page() ) {
			$need_styles = array();
			while ( have_rows( 'content' ) ) {
				the_row();
				$layout = get_row_layout();

				if ( isset( static::$blocks[ $layout ] ) ) {
					foreach ( static::$blocks[ $layout ] as $style ) {
						$need_styles[] = $style;
					}
				}
			}
			foreach ( array_unique( $need_styles ) as $style ) {
				$css .= static::get_css( $style );
			}
		}

		if ( is_callable( static::$callback ) ) {
			$css = call_user_func( static::$callback, $css );
		}

		if ( $css ) {
			// todo: We need to figure out how to escape the CSS output. It seems to be sufficient to make sure it doesn't include a closing tag, and perhaps all external URLs should be replaced.
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '<style>' . $css . '</style>';
		}
	}

	public static function enqueue( array $blocks = array(), Closure $callback = null ) : void {
		if ( $callback ) {
			static::$callback = $callback;
		}

		static::$blocks = $blocks;
	}

	public static function get_css( string $style ) : string {
		$url  = WLD_File::get_assets_url();
		$path = WLD_File::get_assets_path();

		return str_replace(
			'url(../',
			'url(' . $url,
			WLD_Filesystem::get_file_contents( $path . 'css/' . $style )
		);
	}
}
