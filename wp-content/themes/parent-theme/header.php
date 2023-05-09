<?php
/** @noinspection HtmlRequiredTitleElement */

echo '<!DOCTYPE html>';

echo wp_kses(
	'<html ' . get_language_attributes() . '>',
	array(
		'html' => array(
			'dir'    => true,
			'lang'   => true,
			'style'  => true,
			'class'  => true,
			'id'     => true,
			'data-*' => true,
		),
	)
);

echo '<head>';
wp_head();
echo '</head>';

echo '<body class="' . esc_attr( implode( ' ', get_body_class() ) ) . '">';
echo '<a class="screen-reader-text" href="#page-main">' . esc_html__( 'Skip to content', 'theme' ) . '</a>';

wp_body_open();

get_template_part( 'template-parts/header' );
get_template_part( 'template-parts/breadcrumb' );

echo '<main class="page-main" id="page-main" tabindex="">';
