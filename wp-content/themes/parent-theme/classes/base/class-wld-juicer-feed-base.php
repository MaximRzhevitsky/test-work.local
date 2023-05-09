<?php

class WLD_Juicer_Feed_Base {
	public static function init( $feed_name_or_names ) : void {
		foreach ( (array) $feed_name_or_names as $feed_name ) {
			WLD_Delay_Scripts::enqueue_view( 'juicerembed-' . $feed_name, '.juicer-feed' );
		}
	}

	public static function the( string $feed_name, array $feed_attributes = array() ) : void {
		$feed_attributes['name'] = $feed_name;

		if ( class_exists( 'Juicer_Feed' ) ) {
			$juicer_feed = new Juicer_Feed();
			echo wp_kses( $juicer_feed->render( $feed_attributes ), WLD_KSES::get_by_tag( 'a' ) );
		}
	}
}
