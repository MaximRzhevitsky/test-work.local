<?php

class WLD_ACF_Search_Base {

	public static function init() : void {
		add_filter( 'posts_join', array( static::class, 'join' ) );
		add_filter( 'posts_where', array( static::class, 'where' ) );
		add_filter( 'posts_distinct', array( static::class, 'distinct' ) );
	}

	public static function join( string $join ) : string {
		global $wpdb;
		if ( is_search() ) {
			$join .= '
			LEFT JOIN ' . $wpdb->postmeta . ' AS wld_acf_meta ON ' . $wpdb->posts . '.ID = wld_acf_meta.post_id ';
		}

		return $join;
	}

	public static function where( string $where ) : string {
		global $wpdb;
		if ( is_search() ) {
			$where = preg_replace(
				"/\(\s*$wpdb->posts.post_title\s+LIKE\s*('[^']+')\s*\)/",
				"($wpdb->posts.post_title LIKE $1) OR (wld_acf_meta.meta_value LIKE $1)",
				$where
			);
		}

		return $where;
	}

	public static function distinct( string $where ) : string {
		if ( is_search() ) {
			return 'DISTINCT';
		}

		return $where;
	}
}
