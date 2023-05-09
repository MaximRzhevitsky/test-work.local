<?php
class WLD_WC_OF_Loop {
	public static function init() : void {
		add_action(
			'woocommerce_after_shop_loop_item_title',
			array( self::class, 'display_product_rating' ),
			5
		);

		add_filter(
			'woocommerce_output_related_products_args',
			array( static::class, 'output_related_products_args' )
		);

		add_action(
			'woocommerce_before_shop_loop',
			array( static::class, 'wrapper_shop_before' ),
			-1,
		);

		add_action(
			'woocommerce_after_shop_loop',
			array( static::class, 'wrapper_shop_after' ),
			11,
		);

		add_action(
			'woocommerce_pagination_args',
			array( static::class, 'pagination' ),
		);
	}

	public static function pagination( $data ) {
		$data['prev_text'] = esc_html( 'Previous' );
		$data['next_text'] = esc_html( 'Next' );

		return $data;
	}

	public static function wrapper_shop_after() : void {
		if ( ! is_shop() ) {
			return;
		}
		echo '</div></div></section>';
	}

	public static function wrapper_shop_before() : void {
		if ( ! is_shop() ) {
			return;
		}
		?>
		<section class="shop">
			<div class="inner">
				<div class="filters">
					<div class="filters-manage">
						<label for="filters-manage">Filter</label>
						<select name="filters-manage" id="filters-manage">
							<option value="collapse_all">Collapse All</option>
							<option value="expand_all" selected>Expand All</option>
							<option value="expand_marked">Expand Marked</option>
						</select>
						<button class="close-filters-manage">Close</button>
					</div>
					<?php echo do_shortcode( '[wpf-filters id=3]' ); ?>
				</div>
				<div class="woocommerce-products">
		<?php
	}

	public static function output_related_products_args( $args ) {
		$args['posts_per_page'] = 7;
		return $args;
	}

	public static function display_product_rating(): void {
		self::display_stars_rating_custom( 'reviews' );
	}

	public static function display_stars_rating_custom( $args ) {
		if ( empty( $args ) ) {
			return;
		}

		global $product;

		$average = $product->get_average_rating();
		$count   = $product->get_review_count();
		?>
		<div class="main-rating">
			<div class="rating_count">
				<?php
				if ( 'all' === $args ) {
					echo '<span>' . esc_html( $average ) . '</span>';
				}
				?>
				<div class="rating-result">
					<?php
					for ( $i = 1; $i <= 5; $i ++ ) {
						$half_or_zero_class = $average > $i - 1 ? 'active half' : '';
						$class              = $i <= $average ? 'active' : $half_or_zero_class;
						echo '<span class="' . esc_attr( $class ) . '"></span>';
					}
					?>
					<?php
					if ( 'all' === $args || 'reviews' === $args ) {
						$text = 1 === $count ? 'review' : 'reviews';
						echo '<div class="rating-count">' . esc_html( $average ) . '</div>';
					}
					?>
				</div>
			</div>
		</div>
		<?php
	}

	public static function display_stars_rating_reviews_custom( $rating ) {
		?>
		<div class="main-rating">
			<div class="rating_count">
				<div class="rating-result">
					<?php
					for ( $i = 1; $i <= 5; $i ++ ) {
						$half_or_zero_class = $rating > $i - 1 ? 'active half' : '';
						$class              = $i <= $rating ? 'active' : $half_or_zero_class;
						echo '<span class="' . esc_attr( $class ) . '"></span>';
					}
					?>
					<?php echo '<div class="rating-count">rating</div>'; ?>
				</div>
			</div>
		</div>
		<?php
	}
}
