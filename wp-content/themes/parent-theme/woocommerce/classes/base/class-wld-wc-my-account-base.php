<?php


class WLD_WC_My_Account_Base {
	public static function init() : void {
		add_filter(
			'woocommerce_login_redirect',
			array( static::class, 'login_redirect' ),
			10,
			2
		);
		add_filter(
			'woocommerce_process_registration_errors',
			array( static::class, 'validate_register_fields' )
		);
		add_action(
			'woocommerce_created_customer',
			array( static::class, 'save_register_fields' )
		);
		remove_action(
			'woocommerce_register_form',
			'wc_registration_privacy_policy_text',
			20
		);
		add_filter(
			'woocommerce_get_privacy_policy_text',
			array( static::class, 'change_privacy_policy' ),
		);
		add_filter(
			'woocommerce_address_to_edit',
			array( static::class, 'address_to_edit_fields' ),
		);
		add_filter(
			'woocommerce_account_downloads_columns',
			array( static::class, 'account_downloads_columns' ),
		);
		add_action(
			'woocommerce_account_downloads_column_download-file',
			array( static::class, 'account_downloads_column_download_file' ),
		);
		add_filter(
			'default_checkout_billing_country',
			array( static::class, 'change_default_checkout_country' )
		);
	}

	public static function change_default_checkout_country() {
		return 'usa';
	}

	public static function account_downloads_column_download_file( $download ) {
		echo '<a href="' . esc_url( $download['download_url'] ) . '" class="woocommerce-MyAccount-downloads-file button alt">' . esc_html( 'Download' ) . '</a>';
	}

	public static function account_downloads_columns( $data ) {
		unset( $data['download-remaining'], $data['download-expires'] );
		return $data;
	}

	public static function address_to_edit_fields( $address ) {
		foreach ( $address as $key => $value ) {
			if ( 'shipping_address_1' === $key ) {
				$address[ $key ]['label'] = 'Street Address';
			}
			if ( 'shipping_city' === $key ) {
				$address[ $key ]['label'] = 'Town / City';
			}
			if ( 'shipping_postcode' === $key ) {
				$address[ $key ]['label'] = 'ZIP';
			}
			if ( 'billing_phone' === $key ) {
				$address[ $key ]['class'][0] = 'form-row-first';
			}
		}

		return $address;
	}

	public static function change_privacy_policy( $text ) {
		$privacy_policy = '<a href="' . esc_url( home_url( '/privacy-policy/' ) ) . '" class="woocommerce-privacy-policy-link" target="_blank">' . esc_html( 'Privacy' ) . '</a>';

		return str_replace(
			'[privacy_policy]',
			$privacy_policy,
			$text
		);
	}

	public static function login_redirect( string $redirect, WP_User $user ) : string {
		if (
			wc_get_checkout_url() !== $redirect &&
			( $user->has_cap( 'edit_posts' ) || $user->has_cap( 'manage_woocommerce' ) )
		) {
			$redirect = admin_url();
		}

		return $redirect;
	}

	public static function validate_register_fields( WP_Error $errors ) : WP_Error {
		$first_name = $_POST['first_name'] ?? ''; // phpcs:ignore
		if ( empty( $first_name ) ) {
			$errors->add(
				'required',
				__( 'Please enter first name.', 'theme' )
			);
		}

		$last_name = $_POST['last_name'] ?? ''; // phpcs:ignore
		if ( empty( $last_name ) ) {
			$errors->add(
				'required',
				__( 'Please enter last name.', 'theme' )
			);
		}

		$email_1 = $_POST['email'] ?? ''; // phpcs:ignore
		$email_2 = $_POST['email_confirm'] ?? ''; // phpcs:ignore
		if ( empty( $email_2 ) ) {
			$errors->add(
				'required',
				__( 'Please enter confirm email.', 'theme' )
			);
		} elseif ( $email_1 !== $email_2 ) {
			$errors->add(
				'email',
				__( 'Confirm email do not match email.', 'theme' )
			);
		}

		if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) {
			$password_1 = $_POST['password'] ?? ''; // phpcs:ignore
			$password_2 = $_POST['password_confirm'] ?? ''; // phpcs:ignore

			if ( empty( $password_1 ) || empty( $password_2 ) ) {
				$errors->add(
					'password',
					__( 'Please enter password.', 'theme' )
				);
			} elseif ( $password_1 !== $password_2 ) {
				$errors->add(
					'password',
					__( 'Confirm password do not match password.', 'theme' )
				);
			}
		}

		return $errors;
	}

	public static function save_register_fields( $customer_id ) : void {
		try {
			$customer = new WC_Customer( $customer_id );
		} catch ( Exception ) {
			return;
		}

		$email      = wc_clean( $_POST['email'] ?? '' ); // phpcs:ignore
		$first_name = wc_clean( $_POST['first_name'] ?? '' ); // phpcs:ignore
		$last_name  = wc_clean( $_POST['last_name'] ?? '' ); // phpcs:ignore

		$customer->set_first_name( $first_name );
		$customer->set_last_name( $last_name );
		$customer->set_billing_first_name( $first_name );
		$customer->set_billing_last_name( $last_name );
		$customer->set_billing_email( $email );
		$customer->set_display_name( $customer->get_first_name() . ' ' . $customer->get_last_name() );
		$customer->save();
	}
}
