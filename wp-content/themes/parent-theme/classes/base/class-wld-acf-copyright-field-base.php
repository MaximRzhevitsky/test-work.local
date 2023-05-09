<?php

class WLD_ACF_Copyright_Field_Base extends WLD_ACF_Replace_Text_Field {
	public function initialize() : void {
		parent::initialize();
		$this->name  = 'copyright';
		$this->label = __( 'Copyright', 'theme' );
	}

	/** @noinspection PhpUnused */
	public function prepare_field( array $field ) : array {
		if ( $field['instructions'] ) {
			$field['instructions'] .= '<br>';
		}

		$field['instructions'] .= __( '%Y% - current year', 'theme' );

		return $field;
	}

	public function format_value( ?string $value, $post_id, array $field ) : string {
		$value = $this->pre_formatting( $value, $field );

		return str_replace( '%Y%', wp_date( 'Y' ), $value );
	}
}
