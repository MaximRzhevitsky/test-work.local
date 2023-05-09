<?php
$url       = rawurlencode( get_the_permalink() );
$title     = rawurlencode( get_the_title() );
$gmail_url = "https://mail.google.com/mail/?view=cm&fs=1&tf=1&to=&su=&body=+{$url}+&ui=2&tf=1&pli=1"
?>
<div class="social-links social-links_share">
	<h3 class="social-links__title"><?php esc_html_e( 'Don’t forget to share this post!', 'parent-theme' ); ?></h3>
	<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $url; ?>"
	   target="_blank" class="social-links__link" rel="noopener">
		<img src="<?php echo get_stylesheet_directory_uri() . '/assets/images/social-icons/facebook.svg'; ?>" alt="Facebook" class="social-links__image">
		<span class="social-links__text"><?php esc_html_e( 'Facebook', 'parent-theme' ); ?></span>
	</a>
	<a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $url; ?>&amp;title=<?php echo $title; ?>"
	   target="_blank" class="social-links__link" rel="noopener">
		<img src="<?php echo get_stylesheet_directory_uri() . '/assets/images/social-icons/linkedin.svg'; ?>" alt="LinkedIn" class="social-links__image">
		<span class="social-links__text"><?php esc_html_e( 'LinkedIn', 'parent-theme' ); ?></span>
	</a>
	<a href="<?php echo $gmail_url; ?>" class="social-links__link">
		<img src="<?php echo get_stylesheet_directory_uri() . '/assets/images/social-icons/gmail.svg'; ?>" alt="Gmail" class="social-links__image">
		<span class="social-links__text"><?php esc_html_e( 'Gmail', 'parent-theme' ); ?></span>
	</a>
</div>
