<?php

class WLD_File_Base {
	public static function get_url( string $filename = '' ) : string {
		return WLD_Theme::$child_url . $filename;
	}

	public static function get_parent_url( string $filename = '' ) : string {
		return WLD_Theme::$parent_url . $filename;
	}

	public static function get_assets_url( string $filename = '' ) : string {
		return static::get_url( 'assets/' . $filename );
	}

	public static function get_path( string $filename = '' ) : string {
		return WLD_Theme::$child_path . $filename;
	}

	public static function get_parent_path( string $filename = '' ) : string {
		return WLD_Theme::$parent_path . $filename;
	}

	public static function get_assets_path( string $filename = '' ) : string {
		return static::get_path( 'assets/' . $filename );
	}
}
