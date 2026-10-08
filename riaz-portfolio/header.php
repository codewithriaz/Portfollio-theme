<?php
/**
 * Site header: document head, skip link, progress bar and masthead.
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;
?><!doctype html>
<html class="no-js" <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="<?php echo esc_attr( riaz_mod( 'color_dark' ) ); ?>">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?> data-home-url="<?php echo esc_url( home_url( '/' ) ); ?>">
<?php wp_body_open(); ?>
<a class="skip-link" href="#main"><?php esc_html_e( 'Skip to content', 'riaz-portfolio' ); ?></a>
<?php if ( riaz_mod_bool( 'progress_enable' ) ) : ?>
	<div class="scroll-progress" aria-hidden="true" data-scroll-progress></div>
<?php endif; ?>
<?php
if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'header' ) ) {
	get_template_part( 'template-parts/header/site-header' );
}
?>
<div class="page-frame">
