<?php

class WLD_Local_External_Scripts_Base {
	public const CRON_HOOK = 'wld_local_external_scripts';

	public static function init() : void {
		add_action(
			'switch_theme',
			array( static::class, 'add_cron' )
		);
		add_action(
			static::CRON_HOOK,
			array( static::class, 'run_cron' )
		);
	}

	public static function local( string $external_url, string $file_name = '' ) : string {
		$file_name  = static::get_file_name( $file_name ?: $external_url );
		$local_path = static::get_dir() . $file_name;
		$local_url  = static::get_url() . $file_name;

		if ( file_exists( $local_path ) ) {
			return $local_url;
		}

		return static::write_file( $external_url, $file_name );
	}

	public static function enqueue( string $handle ) : void {
		global $wp_scripts;

		if ( in_array( $handle, $wp_scripts->queue, true ) ) {
			$wp_scripts->registered[ $handle ]->src = static::local( $wp_scripts->registered[ $handle ]->src, $handle );
		}
	}

	public static function add_cron() : void {
		wp_schedule_event( time(), 'hourly', static::CRON_HOOK );
	}

	public static function run_cron() : void {
		$local_paths = glob( static::get_dir() . '*.js' );
		if ( $local_paths ) {
			foreach ( $local_paths as $local_path ) {
				$info_path = $local_path . '.info';
				if ( file_exists( $info_path ) ) {
					$info = WLD_Filesystem::get_file_contents( $info_path );
					if ( $info ) {
						$external_url = explode( ':', $info )[0];
						$file_name    = explode( ':', $info )[1];
						static::write_file( $external_url, $file_name );
						continue;
					}

					WLD_Filesystem::delete_file_or_folder( $info_path );
				}

				WLD_Filesystem::delete_file_or_folder( $local_path );
			}
		}
	}

	public static function write_file( string $external_url, string $file_name = '' ) : string {
		$response = wp_remote_get( $external_url );
		if ( ! is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) === 200 ) {
			$contents   = wp_remote_retrieve_body( $response );
			$local_path = static::get_dir() . $file_name;
			$local_url  = static::get_url() . $file_name;

			if ( WLD_Filesystem::put_file_contents( $local_path, $contents ) ) {
				WLD_Filesystem::put_file_contents( $local_path . '.info', $external_url . ':' . $file_name );

				return $local_url;
			}
		}

		return $external_url;
	}

	protected static function get_dir() : string {
		static $dir;

		if ( null === $dir ) {
			$dir = wp_upload_dir()['basedir'] . '/wld-externals/';
		}

		return $dir;
	}

	protected static function get_url() : string {
		static $dir;

		if ( null === $dir ) {
			$dir = wp_upload_dir()['baseurl'] . '/wld-externals/';
		}

		return $dir;
	}

	protected static function get_file_name( string $external_url ) : string {
		$basename = sanitize_file_name( basename( $external_url ) );
		if ( str_contains( $basename, '.' ) ) {
			$basename .= '.js';
		}

		return $basename;
	}
}
