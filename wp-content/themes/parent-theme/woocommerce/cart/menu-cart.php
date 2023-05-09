<?php /** @noinspection PhpUndefinedMethodInspection, PhpUndefinedClassInspection, PhpUndefinedFunctionInspection */
/** @var array $args */

defined( 'ABSPATH' ) || exit;

$count     = WC()->cart->get_cart_contents_count();
$items     = '';

if ( $count ) {
	$items = sprintf( // translators: %d count in cart
		_n( '%d item', '%d items', $count, 'theme' ),
		$count
	);
}
?>
<aside class="menu-cart" aria-hidden="<?php echo true === $args['hidden'] ? 'true' : 'false'; ?>">
	<div class="menu-cart__wrapper">
		<div class="menu-cart__header">
			<h2 class="menu-cart__title">
				<?php esc_html_e( 'Cart', 'theme' ); ?>
				<?php if ( $items ) : ?>
					<?php echo '<span class="menu-cart__count">' . esc_html( $items ) . '</span>'; ?>
				<?php endif; ?>
			</h2>
			<a href="#" class="menu-cart__close" data-close aria-expanded="false">
				<span class="screen-reader-text">
					<?php esc_html_e( 'Close cart', 'theme' ); ?>
				</span>
			</a>
		</div>
		<?php if ( ! WC()->cart->is_empty() ) : ?>
			<?php woocommerce_mini_cart(); ?>

		<?php else : ?>
			<div class="empty">
				<?php esc_html_e( 'Empty Cart', 'theme' ); ?>
			</div>
		<?php endif; ?>
	</div>
</aside>
