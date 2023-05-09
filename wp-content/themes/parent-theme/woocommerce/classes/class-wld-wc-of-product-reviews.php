<?php

class WLD_WC_OF_Product_Reviews {
	public static function init(): void {
		add_filter(
			'comment_form_fields',
			array( static::class, 'change_rating_and_comment_fields' )
		);
		add_filter(
			'comment_form_fields',
			array( static::class, 'change_author_field' )
		);
		add_filter(
			'option_show_comments_cookies_opt_in',
			array( static::class, 'disabled_cookies_field' )
		);
		add_filter(
			'woocommerce_product_review_comment_form_args',
			array( static::class, 'change_notes_before' )
		);
		add_filter(
			'woocommerce_reviews_title',
			array( static::class, 'change_section_title' ),
			10,
			2
		);
		add_action(
			'woocommerce_review_meta',
			array( static::class, 'review_display_meta' ),
		);
		add_action(
			'woocommerce_review_after_comment_text',
			array( static::class, 'review_display_share_and_vote' ),
		);
		add_action(
			'comment_form_top',
			array( static::class, 'remove_fields_from_front_end' ),
		);
		add_action(
			'woocommerce_comment_pagination_args',
			array( static::class, 'change_pagination' ),
		);
		add_action(
			'comment_post',
			array( static::class, 'save_comment_review' )
		);

		remove_action( 'woocommerce_review_before_comment_meta', 'woocommerce_review_display_rating' );
		remove_action( 'woocommerce_review_meta', 'woocommerce_review_display_meta' );
	}

	public static function save_comment_review( $comment_id ) : void {
		if ( isset( $_POST['title_comment'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Missing
			update_comment_meta( $comment_id, 'title', esc_attr( $_POST['title_comment'] ) ); //phpcs:ignore WordPress.Security.NonceVerification.Missing
		}
	}

	public static function the_score_and_title_field(): void {
		$html = '';

		if ( wc_review_ratings_enabled() ) {
			$label    = esc_html__( 'Score:', 'woocommerce' );
			$required = wc_review_ratings_required() ? '&nbsp;<span class="required">*</span>' : '';

			$html .= '<div class="comment-form-rating">
				<label for="rating">' . $label . $required . '</label>
				<select name="rating" id="rating" required>
					<option value="">' . esc_html__( 'Rate&hellip;', 'woocommerce' ) . '</option>
					<option value="5">' . esc_html__( 'Perfect', 'woocommerce' ) . '</option>
					<option value="4">' . esc_html__( 'Good', 'woocommerce' ) . '</option>
					<option value="3">' . esc_html__( 'Average', 'woocommerce' ) . '</option>
					<option value="2">' . esc_html__( 'Not that bad', 'woocommerce' ) . '</option>
					<option value="1">' . esc_html__( 'Very poor', 'woocommerce' ) . '</option>
				</select>
			</div>';
		}

		$html .= '<p class="comment-form-title">';
		$html .= '<label for="title">' . __( 'Title', 'parent-theme' ) . '&nbsp;<span class="required">*</span></label>';
		$html .= '<input id="title" name="title_comment" type="text" value="" size="30" required/></p>';

		echo $html;
	}

	public static function change_rating_and_comment_fields( array $fields ): array {
		$label             = esc_html__( 'Review', 'woocommerce' );
		$fields['comment'] = '<p class="comment-form-comment">
			<label for="comment">' . $label . '&nbsp;<span class="required">*</span></label>
			<textarea id="comment" name="comment" cols="45" rows="8" required></textarea>
		</p>';

		return $fields;
	}

	public static function change_author_field( array $fields ): array {
		$html = '';
		$key  = 'author';

		$html .= '<p class="comment-form-' . esc_attr( $key ) . '">';
		$html .= '<label for="' . esc_attr( $key ) . '">' . __( 'Use your name', 'parent-theme' );
		$html .= '&nbsp;<span class="required">*</span>';
		$html .= '</label><input id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" type="text" value="" size="30" required/></p>';

		$fields[ $key ] = $html;

		return $fields;
	}

	public static function disabled_cookies_field(): bool {
		return false;
	}

	public static function change_notes_before( array $args ): array {
		$args['title_reply']          = esc_html__( 'Write a review', 'parent-theme' );
		$args['label_submit']         = esc_html__( 'Post', 'parent-theme' );
		$args['comment_notes_before'] = '';

		return $args;
	}

	public static function change_section_title( $reviews_title, $count ): string {
		return $count . ' ' . esc_html__( 'Reviews', 'parent-theme' );
	}

	public static function review_display_meta(): void {
		global $comment;

		$verified = wc_review_is_from_verified_owner( $comment->comment_ID );

		if ( '0' === $comment->comment_approved ) { ?>
			<p class="meta">
				<em class="woocommerce-review__awaiting-approval">
					<?php esc_html_e( 'Your review is awaiting approval', 'woocommerce' ); ?>
				</em>
			</p>
		<?php } else { ?>
			<?php woocommerce_review_display_rating(); ?>
			<p class="meta">
				<strong class="woocommerce-review__author"><?php comment_author(); ?> </strong>
				<?php
				if ( $verified && 'yes' === get_option( 'woocommerce_review_rating_verification_label' ) ) {
					echo '<em class="woocommerce-review__verified verified">' . esc_attr__( 'Verified Buyer', 'parent-theme' ) . '</em> ';
				}
				?>
			</p>
		<?php } ?>
		<time class="woocommerce-review__published-date" datetime="<?php echo esc_attr( get_comment_date( 'c' ) ); ?>">
			<?php echo esc_html( get_comment_date( 'm.d.y' ) ); ?>
		</time>
		<?php
		$title = get_comment_meta( $comment->comment_ID, 'title', true );
		echo $title ? '<h3>' . esc_html( $title ) . '</h3>' : '';
	}

	public static function review_display_share_and_vote( WP_Comment $comment ): void {
		echo '<div class="comment-footer">';
		do_action( 'comment_footer', $comment );
		echo '</div>';
	}

	public static function remove_fields_from_front_end(): void {
		acf_form_data(
			array(
				'screen'  => 'comment',
				'post_id' => false,
			)
		);
		wld_remove_filter_for_class( 'comment_form_field_comment', 'acf_form_comment', 'comment_form_field_comment', 999 );

		echo '<p class="comment-notes">' . esc_html__( '*Indicates a required field', 'parent-theme' ) . '</p>';
		static::the_score_and_title_field();
	}

	public static function change_pagination( array $args ): array {
		$args['prev_text'] = esc_html__( 'Previous', 'parent-theme' );
		$args['next_text'] = esc_html__( 'Next', 'parent-theme' );

		return $args;
	}
}
