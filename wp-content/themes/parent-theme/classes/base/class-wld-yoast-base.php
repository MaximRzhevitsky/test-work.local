<?php

class WLD_Yoast_Base {
	public static function init() : void {
		add_filter(
			'wpseo_metadesc',
			array( static::class, 'add_page_number' )
		);
		add_filter(
			'wpseo_title',
			array( static::class, 'add_page_number' )
		);
	}

	public static function add_page_number( $title_or_desc ) : string {
		global $page;

		$paged = 1 < (int) $page ? $page : get_query_var( 'paged', 1 );
		if ( $paged > 1 ) {
			// translators: %d: number page
			$title_or_desc .= ' | ' . sprintf( __( 'Page %d', 'theme' ), $paged );
		}

		return $title_or_desc;
	}
}
