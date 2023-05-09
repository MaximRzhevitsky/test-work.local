<?php

class WLD_GF_Base {
	public static function init() : void {
		add_filter( 'gform_enable_field_label_visibility_settings', '__return_true' );

		// todo: Remove this in favor of native GF classes.
		add_action(
			'gform_field_css_class',
			array( static::class, 'add_type_field_classnames' ),
			10,
			2
		);
	}

	public static function add_type_field_classnames( string $css_class, GF_Field $field ) : string {
		$classname = 'gfield_' . $field->get_input_type();
		if ( ! str_contains( $css_class, $classname ) ) {
			$css_class .= ' ' . $classname;
		}

		return $css_class;
	}
}
