<section class="error-404">
		<?php while ( wld_loop( 'wld_404' ) ) : ?>
			<div class="inner">
				<?php wld_the( 'image', '500x0', '<div class="error-404__image">' ); ?>
				<?php wld_the( 'title', 'error-404__title' ); ?>
				<?php wld_the( 'text', '', 'error-404__text' ); ?>
				<?php while ( wld_loop( 'links', '<div class="error-404__links">' ) ) : ?>
					<?php wld_the( 'link', 'error-404__link' ); ?>
					<?php if ( 0 === get_row_index() % 3 ) : ?>
						<?php echo '<br>'; ?>
					<?php endif; ?>
				<?php endwhile; ?>
				<?php wld_the( 'button', 'error-404__button btn', '<p>' ); ?>
			</div>
			<?php if ( wld_get( 'search_form_enabled', 'options' ) ) : ?>
				<section class="search-form search-form_error-404-page">
					<div class="search-form__wrapper inner">
						<?php if ( is_search() ) : ?>
							<?php wld_the( 'wld_search_form_image', '', array(
								'class' => 'search-form__image object-fit object-fit-cover'
							) ); ?>
						<?php endif; ?>
						<?php if ( wld_has( 'search_form_title' ) ) : ?>
							<?php wld_the( 'search_form_title', 'search-form__title' ); ?>
						<?php endif; ?>
						<form class="search-form__form" role="search" method="get" action="/">
							<label class="search-form__label">
								<span class="screen-reader-text">Search for:</span>
								<input class="search-form__input" type="search" placeholder="Type your question" value="" name="s">
							</label>
							<input class="search-form__submit" type="submit" value="Go!">
						</form>
					</div>
				</section>
			<?php endif; ?>
		<?php endwhile; ?>
</section>
