<section class="search-form search-form_search-results-page">
	<?php while( wld_loop( 'wld_search_form', '<div class="search-form__wrapper inner">' ) ) : ?>
			<?php wld_the( 'image', '1170x0', array(
				'class' => 'search-form__image object-fit object-fit-cover',
			) ); ?>
			<?php wld_the( 'title', 'search-form__title' ); ?>
			<form class="search-form__form" role="search" method="get" action="/">
				<label class="search-form__label">
					<span class="screen-reader-text">Search for:</span>
					<input class="search-form__input" type="search" placeholder="Type your question" value="" name="s">
				</label>
				<input class="search-form__submit" type="submit" value="Search">
			</form>
	<?php endwhile; ?>
</section>
