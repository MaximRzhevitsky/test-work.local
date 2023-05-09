<?php

class WLD_Favicon_Base {
	public static function init() : void {
		remove_action( 'wp_head', 'wp_site_icon', 99 );
		add_filter(
			'acf/update_value/name=wld_other_favicon',
			array( static::class, 'save_favicon' )
		);
		add_action(
			'wp_head',
			array( static::class, 'the_favicon' )
		);
		add_action(
			'admin_head',
			array( static::class, 'the_favicon' )
		);
		add_filter(
			'wp_check_filetype_and_ext',
			array( static::class, 'allow_ico_mime' ),
			10,
			5
		);
	}

	public static function save_favicon( $value ) {
		$favicon = $value ? wp_get_attachment_image_url( (int) $value, 'full' ) : '';
		if ( $favicon ) {
			$favicon = preg_replace( '/^https?:/', '', $favicon );

			/** @noinspection HtmlUnknownTarget */
			update_option(
				'wld_other_favicon_html',
				sprintf( '<link rel="shortcut icon" href="%s">', esc_url( $favicon ) ),
				true
			);
		} else {
			delete_option( 'wld_other_favicon_html' );
		}

		return $value;
	}

	public static function the_favicon() : void {
		echo wp_kses(
			get_option( 'wld_other_favicon_html' ),
			array(
				'link' => array(
					'rel'  => true,
					'href' => true,
				),
			)
		);
	}

	public static function allow_ico_mime( array $data, $file, $filename, $mimes, string $real_mime ) : array {
		if ( 'image/vnd.microsoft.icon' === $real_mime ) {
			if ( current_user_can( 'manage_options' ) ) {
				$data['ext']  = 'ico';
				$data['type'] = 'image/x-icon';
			} else {
				$data['ext']  = false;
				$data['type'] = false;
			}
		}

		return $data;
	}
}
