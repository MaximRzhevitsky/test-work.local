<?php
/**
 * Login Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-login.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$username = wp_unslash( $_POST['username'] ?? '' ); // phpcs:ignore
$email    = wp_unslash( $_POST['email'] ?? '' ); // phpcs:ignore
$terms    = wpautop(
	str_replace(
		array(
			__( 'privacy policy', 'woocommerce' ),
			__( 'terms and conditions', 'woocommerce' ),
		),
		array(
			get_the_title( wc_privacy_policy_page_id() ),
			get_the_title( wc_terms_and_conditions_page_id() ),
		),
		wc_replace_policy_page_link_placeholders( wc_get_privacy_policy_text( 'registration' ) )
	)
);
?>
<?php if ( ! WC()->cart->is_empty() ) : ?>
	<div class="guest-checkout">
		<h2><?php esc_html_e( 'Guest Checkout', 'theme' ); ?></h2>
		<p>
			<?php
			esc_html_e(
				'You can check out without creating an account. You will have a chance to create an account later.',
				'theme'
			);
			?>
		</p>
		<a class="btn" href="<?php echo esc_url( wc_get_checkout_url() ); ?>">
			<?php esc_html_e( 'Checkout as Guest', 'theme' ); ?>
		</a>
	</div>
<?php endif; ?>
<?php do_action( 'woocommerce_before_customer_login_form' ); ?>
<div class="section-auth">
	<ul class="tabs-nav">
		<li><?php esc_html_e( 'Sign In', 'woocommerce' ); ?></li>
		<?php if ( 'yes' === get_option( 'woocommerce_enable_myaccount_registration' ) ) : ?>
			<li data-hash="#registration"><?php esc_html_e( 'Create an Account', 'woocommerce' ); ?></li>
		<?php endif; ?>
	</ul>
	<div class="tabs">
		<div class="tab">
			<form class="woocommerce-form woocommerce-form-login login" method="post">
				<?php do_action( 'woocommerce_login_form_start' ); ?>
				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label for="username">
						<?php esc_html_e( 'Email address', 'theme' ); ?>&nbsp;<abbr
							class="required" title="required">*</abbr>
					</label>
					<input type="text"
						   class="woocommerce-Input woocommerce-Input--text input-text"
						   name="username"
						   id="username"
						   autocomplete="username"
						   value="<?php echo esc_attr( $username ); ?>">
				</p>
				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label for="password">
						<?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<abbr
							class="required" title="required">*</abbr>
					</label>
					<input class="woocommerce-Input woocommerce-Input--text input-text"
						   type="password" name="password"
						   id="password"
						   autocomplete="current-password">
				</p>
				<?php do_action( 'woocommerce_login_form' ); ?>
				<p class="form-row form-row-rememberme">
					<input class="woocommerce-form__input woocommerce-form__input-checkbox"
						   name="rememberme"
						   type="checkbox"
						   id="rememberme"
						   value="forever">
					<label
						for="rememberme"
						class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme">
						<span><?php esc_html_e( 'Remember me', 'woocommerce' ); ?></span>
					</label>
					<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"
					   class="woocommerce-LostPassword lost_password">
						<?php esc_html_e( 'Forgot Password?', 'theme' ); ?>
					</a>
				</p>
				<p>
					<?php
					wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' );
					if ( wc_get_raw_referer() ) {
						printf(
							'<input type="hidden" name="redirect" value="%s">',
							esc_url( wc_get_raw_referer() )
						);
					}

					$class = wc_wp_theme_get_element_class_name( 'button' )
					?>
					<button type="submit"
							class="woocommerce-button button woocommerce-form-login__submit<?php echo esc_attr( $class ? ' ' . $class : '' ); ?>"
							name="login"
							value="<?php esc_attr_e( 'Sign In', 'theme' ); ?>"
					><?php esc_html_e( 'Sign In', 'theme' ); ?></button>
				</p>
				<?php do_action( 'woocommerce_login_form_end' ); ?>
			</form>
		</div>
		<?php if ( 'yes' === get_option( 'woocommerce_enable_myaccount_registration' ) ) : ?>
			<div class="tab">
				<form method="post"
					  action="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>#registration"
					  class="woocommerce-form woocommerce-form-register register"
					<?php do_action( 'woocommerce_register_form_tag' ); ?>>
					<?php do_action( 'woocommerce_register_form_start' ); ?>
					<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>
						<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
							<label for="reg_username">
								<?php esc_html_e( 'Username', 'woocommerce' ); ?>&nbsp;<abbr
									class="required" title="required">*</abbr>
							</label>
							<input type="text"
								   class="woocommerce-Input woocommerce-Input--text input-text"
								   name="username"
								   id="reg_username"
								   autocomplete="username"
								   value="<?php echo esc_attr( $username ); ?>">
						</p>
					<?php endif; ?>
					<?php
					woocommerce_form_field(
						'first_name',
						array(
							'label'        => __( 'First name', 'theme' ),
							'required'     => true,
							'class'        => array( 'form-row-wide' ),
							'autocomplete' => 'given-name',
							'type'         => 'text',
						),
						$_POST['first_name'] ?? '' // phpcs:ignore
					);
					woocommerce_form_field(
						'last_name',
						array(
							'label'        => __( 'Last name', 'theme' ),
							'required'     => true,
							'class'        => array( 'form-row-wide' ),
							'autocomplete' => 'family-name',
							'type'         => 'text',
						),
						$_POST['last_name'] ?? '' // phpcs:ignore
					);
					?>
					<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
						<label for="reg_email">
							<?php esc_html_e( 'Email', 'theme' ); ?>&nbsp;<abbr
								class="required" title="required">*</abbr>
						</label>
						<input type="email"
							   class="woocommerce-Input woocommerce-Input--text input-text"
							   name="email"
							   id="reg_email"
							   autocomplete="email"
							   value="<?php echo esc_attr( $email ); ?>">
					</p>
					<?php
					woocommerce_form_field(
						'email_confirm',
						array(
							'label'        => __( 'Confirm email', 'theme' ),
							'required'     => true,
							'class'        => array( 'form-row-wide', 'password-field' ),
							'autocomplete' => 'email',
							'type'         => 'email',
						),
						$_POST['email_confirm'] ?? '' // phpcs:ignore
					);
					?>
					<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>
						<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
							<label for="reg_password">
								<?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<abbr
									class="required" title="required">*</abbr>
							</label>
							<input type="password"
								   class="woocommerce-Input woocommerce-Input--text input-text"
								   name="password"
								   id="reg_password"
								   autocomplete="new-password">
						</p>
						<?php
						woocommerce_form_field(
							'password_confirm',
							array(
								'label'        => __( 'Confirm password', 'theme' ),
								'required'     => true,
								'class'        => array( 'form-row-wide', 'password-field' ),
								'autocomplete' => 'new-password',
								'type'         => 'password',
							)
						);
						?>
					<?php else : ?>
						<p>
							<?php
							esc_html_e(
								'A password will be sent to your email address.',
								'woocommerce'
							);
							?>
						</p>
					<?php endif; ?>
					<?php do_action( 'woocommerce_register_form' ); ?>
					<p class="woocommerce-form-row form-row">
						<?php
						wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' );
						if ( wc_get_raw_referer() ) {
							printf(
								'<input type="hidden" name="redirect" value="%s">',
								esc_url( wc_get_raw_referer() )
							);
						}

						$class = wc_wp_theme_get_element_class_name( 'button' );
						?>
						<button type="submit"
								class="woocommerce-Button woocommerce-button button<?php echo esc_attr( $class ? ' ' . $class : '' ); ?> woocommerce-form-register__submit"
								name="register"
								value="<?php esc_attr_e( 'Log In', 'theme' ); ?>"
						><?php esc_html_e( 'Log In', 'theme' ); ?></button>
					</p>
					<?php echo wp_kses_post( $terms ); ?>
					<?php do_action( 'woocommerce_register_form_end' ); ?>
				</form>
			</div>
		<?php endif; ?>
	</div>
</div>

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
