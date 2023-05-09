<?php

class WLD_Enqueue_Base {

	public static function init() : void {
		add_action(
			'wp_enqueue_scripts',
			array( static::class, 'enqueue_base' )
		);
	}

	public static function enqueue_base() : void {
	}

	public static function enqueue_file( string $file_name, array $args = array() ) : string {
		$data = explode( '.', $file_name );
		$type = $data[1];
		$file = sprintf( '%s/%s', $type, $file_name );
		$path = WLD_File::get_assets_path( $file );
		if ( file_exists( $path ) ) {
			$handle    = sprintf( 'theme-%s', $data[0] );
			$version   = static::get_version( $path );
			$theme_url = WLD_File::get_assets_url();
			if ( isset( $args['deps'] ) ) {
				$deps = is_array( $args['deps'] ) ? $args['deps'] : array( $args['deps'] );
			} else {
				$deps = array();
			}
			if ( 'js' === $type ) {
				wp_enqueue_script(
					$handle,
					$theme_url . $file,
					$deps,
					$version,
					$args['in_footer'] ?? true
				);
			} else {
				wp_enqueue_style(
					$handle,
					$theme_url . $file,
					$deps,
					$version,
					$args['media'] ?? 'all'
				);
			}

			return $handle;
		}

		return '';
	}

	public static function get_version( string $path ) : string {
		return (string) filemtime( $path );
	}
}
