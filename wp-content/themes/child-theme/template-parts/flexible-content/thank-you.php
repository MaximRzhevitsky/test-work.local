<section class="thank-you">
	<div class="inner">
		<div class="thank-you__wrapper">
			<?php wld_the( 'title', 'thank-you__title' ); ?>
			<p class="thank-you__subtitle">
				<?php wld_the( 'subtitle' ); ?>
			</p>
			<?php wld_the( 'text' ); ?>
		</div>
		<?php
			$social_links = wld_get( 'social_links' );
			$title_cut    = preg_match( '#<h3[^>]*>(.*?)</h3>#i', $social_links, $title );
			$social_links = str_replace(
				array(
					$title[0],
					'<div class="social-links">',
				),
				array(
					'',
					'<div class="social-links"><h2 class="social-links__title-wrapper"><span class="social-links__title">' . $title[1] . '</span></h2>',
				),
				$social_links
			);
			echo $social_links;
			?>
		<p>
			<?php wld_the( 'back_button' ); ?>
		</p>
	</div>
</section>
