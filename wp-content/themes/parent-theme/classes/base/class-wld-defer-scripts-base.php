<?php

class WLD_Defer_Scripts_Base {
	protected static array $handles = array();

	public static function init() : void {
		add_action(
			'script_loader_tag',
			array( static::class, 'insert' ),
			10,
			2
		);
	}

	public static function add( string $handle, callable $toggle_callback = null ) : void {
		$toggle = true;
		if ( is_callable( $toggle_callback ) ) {
			$toggle = $toggle_callback();
		}

		static::$handles[ $handle ] = $toggle;
	}

	public static function remove( string $handle ) : void {
		if ( isset( static::$handles[ $handle ] ) ) {
			unset( static::$handles[ $handle ] );
		}
	}

	public static function insert( $tag, $handle ) {
		if ( isset( static::$handles[ $handle ] ) && static::$handles[ $handle ] ) {
			return str_replace( ' src', ' defer="defer" src', $tag );
		}

		return $tag;
	}
}
