<?php

class WLD_ACF_Google_Maps_API_Base {
	public static string $api_key = '';

	public static function init() : void {
		add_filter(
			'acf/fields/google_map/api',
			array( static::class, 'set_api_key_in_acf' )
		);
		add_filter(
			'wld_enqueue_get_theme_object',
			array( static::class, 'set_key_in_theme' )
		);
	}

	public static function set_api_key_in_acf( array $api ) : array {
		$api['key'] = static::get_api_key();

		return $api;
	}

	public static function set_key_in_theme( array $theme ) : array {
		$theme['googleMapsApiKey'] = static::get_api_key();

		return $theme;
	}

	public static function get_api_key() : string {
		return empty( static::$api_key ) ? (string) get_field( 'wld_api_google_maps_key', 'option' ) : static::$api_key;
	}
}
