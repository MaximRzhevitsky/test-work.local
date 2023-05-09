<?php

class WLD_Clear_Cache_Menu_Order_Base {
	public static function init() : void {
		add_action(
			'wp_ajax_update-menu-order-tags',
			array( static::class, 'flush' )
		);
	}

	public static function flush() : void {
		wp_cache_flush();
	}
}
