<?php

class WLD_WC_Images_Base {
	public static function init() : void {
		add_filter(
			'wp_get_attachment_metadata',
			array( static::class, 'fix_svg_sizes_php_notice' )
		);
	}

	public static function fix_svg_sizes_php_notice( $data ) {
		if (
			empty( $data['width'] ) &&
			in_array(
				'WC_Regenerate_Images::get_full_size_image_dimensions',
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_wp_debug_backtrace_summary
				wp_debug_backtrace_summary( null, 0, false ),
				true
			)
		) {
			return false;
		}

		return $data;
	}
}
