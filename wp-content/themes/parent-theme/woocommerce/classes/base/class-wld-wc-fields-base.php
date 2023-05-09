<?php /** @noinspection AlterInForeachInspection */

class WLD_WC_Fields_Base {
	public static function init() : void {
		add_filter(
			'woocommerce_billing_fields',
			array( static::class, 'change_address_fields' )
		);
		add_action(
			'woocommerce_before_checkout_process',
			array( static::class, 'enable_create_account_if_has_password' )
		);
		add_filter(
			'woocommerce_default_address_fields',
			array( static::class, 'set_default_address_fields' )
		);
	}

	public static function change_address_fields( array $fields ) : array {
		$key = 'billing_email';
		if ( isset( $fields[ $key ] ) ) {
			unset( $fields[ $key ] );
		}

		return $fields;
	}

	public static function enable_create_account_if_has_password() : void {
		$password = $_POST['account_password'] ?? ''; // phpcs:ignore
		if ( $password && ! is_user_logged_in() ) {
			$_POST['createaccount'] = 1;
		}
	}

	public static function set_default_address_fields( array $fields ) : array {
		if ( isset( $fields['company'] ) ) {
			unset( $fields['company'] );
		}

		if ( isset( $fields['country'] ) ) {
			$fields['country']['priority'] = 65;
		}

		if ( isset( $fields['address_1'] ) ) {
			$fields['address_1']['label']       = esc_html__( 'Address', 'theme' );
			$fields['address_1']['placeholder'] = '';
		}

		if ( isset( $fields['address_2'] ) ) {
			$fields['address_2']['label']       = esc_html__( 'Address 2', 'theme' );
			$fields['address_2']['placeholder'] = '';
			if ( ! empty( $fields['address_2']['label_class'] ) ) {
				foreach ( $fields['address_2']['label_class'] as $i => $class_name ) {
					if ( 'screen-reader-text' === $class_name ) {
						unset( $fields['address_2']['label_class'][ $i ] );
						break;
					}
				}
			}
		}

		if ( isset( $fields['city'] ) ) {
			$fields['city']['label']       = esc_html__( 'City', 'theme' );
			$fields['city']['placeholder'] = '';
		}

		uasort( $fields, 'wc_checkout_fields_uasort_comparison' );

		$i = 0;
		foreach ( $fields as &$field ) {
			$even  = ( $i ++ ) % 2 === 0;
			$field = static::set_align_class( $field, $even );
		}

		return $fields;
	}

	protected static function set_align_class( array $field, bool $even ) : array {
		$remove_classes = array(
			'form-row-first',
			'form-row-last',
			'form-row-wide',
		);

		foreach ( $remove_classes as $remove_class ) {
			$remove_key = array_search( $remove_class, $field['class'], true );
			if ( false !== $remove_key ) {
				unset( $field['class'][ $remove_key ] );
			}
		}

		$field['class'][] = $even ? 'form-row-first' : 'form-row-last';

		return $field;
	}
}
