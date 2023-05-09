<?php

class WLD_ACF_Forms_Field_Base extends acf_field {
	public array $forms = array();

	public function initialize() : void {
		$this->name     = 'forms';
		$this->label    = __( 'Forms', 'theme' );
		$this->category = 'relational';
		$this->defaults = array(
			'return_format' => 'post_object',
			'multiple'      => 0,
			'allow_null'    => 1,
		);
		$this->set_forms();
	}

	/** @noinspection SqlNoDataSourceInspection, SqlResolve, SqlDialectInspection */
	public function set_forms() : void {
		if ( class_exists( 'GFFormsModel' ) ) {
			global $wpdb;

			$wpdb->wld_gf_forms = GFFormsModel::get_form_table_name();
			if ( GFCommon::table_exists( $wpdb->wld_gf_forms ) ) {
				$results = $wpdb->get_results( "SELECT id, title FROM $wpdb->wld_gf_forms WHERE is_trash = 0" );
				if ( $results ) {
					foreach ( $results as $form ) {
						$this->forms[ $form->id ] = $form->title;
					}
				}
			}
		}
	}

	public function render_field_settings( array $field ) : void {
		acf_render_field_setting(
			$field,
			array(
				'label'   => __( 'Return Value', 'theme' ),
				'type'    => 'radio',
				'name'    => 'return_format',
				'layout'  => 'horizontal',
				'choices' => array(
					'post_object' => __( 'Form Object', 'theme' ),
					'id'          => __( 'Form ID', 'theme' ),
				),
			)
		);
		acf_render_field_setting(
			$field,
			array(
				'label'   => __( 'Select multiple values?', 'theme' ),
				'type'    => 'radio',
				'name'    => 'multiple',
				'choices' => array(
					1 => __( 'Yes', 'theme' ),
					0 => __( 'No', 'theme' ),
				),
				'layout'  => 'horizontal',
			)
		);
	}

	/** @noinspection HtmlUnknownAttribute */
	public function render_field( array $field ) : void {
		$multiple = '';
		$field    = array_merge( $this->defaults, $field );
		if ( $field['multiple'] ) {
			$forms = (array) $field['value'];
		} else {
			$field['value'] = (array) $field['value'];
			$forms          = (array) reset( $field['value'] );
		}

		$forms = wp_parse_id_list( $forms );

		if ( $field['multiple'] ) {
			echo '<input type="hidden" name="' . esc_attr( $field['name'] ) . '">';
			$multiple = '[]" multiple="multiple" data-multiple="1';
		}

		echo '<select name="' . esc_attr( $field['name'] . $multiple ) . '">';
		echo '<option value="">' . esc_html__( '- Select a form -', 'theme' ) . '</option>';
		foreach ( $this->forms as $form_id => $form_title ) {
			$is_selected = $forms && in_array( $form_id, $forms, true );
			printf(
				'<option%s value="%s">%s</option>',
				esc_html( $is_selected ? ' selected="selected"' : '' ),
				esc_attr( $form_id ),
				esc_html( $form_title )
			);
		}

		echo '</select>';
	}

	/** @noinspection PhpUnusedParameterInspection */
	public function format_value( $value, $post_id, array $field ) : bool | array | int {
		if ( $field['multiple'] ) {
			$_value = array();
			if ( $value ) {
				if ( 'post_object' === $field['return_format'] ) {
					foreach ( (array) $value as $form_id ) {
						$form = GFFormsModel::get_form( $form_id );
						if ( $form ) {
							$_value[ $form_id ] = (array) $form;
						}
					}
				} else {
					$_value = $value;
				}
			}

			return $_value;
		}

		if ( is_array( $value ) ) {
			$value = reset( $value );
		}

		if ( $value && 'post_object' === $field['return_format'] ) {
			$form = GFFormsModel::get_form( $value );
			if ( $form ) {
				return (array) $form;
			}

			return false;
		}

		return (int) $value;
	}

	public function update_value( $value ) {
		if ( is_array( $value ) ) {
			$value = array_values( array_filter( $value ) );
		}

		return $value;
	}
}
