<?php
/**
 * Template Name: Blank Canvas (no header or footer)
 * Template Post Type: page
 *
 * For landing pages built entirely with blocks or a page builder.
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;
?><!doctype html>
<html class="no-js" <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'rp-canvas' ); ?> data-home-url="<?php echo esc_url( home_url( '/' ) ); ?>">
<?php wp_body_open(); ?>
<main id="main" class="site-main page-frame">
	<?php
	while ( have_posts() ) :
		the_post();
		get_template_part( 'template-parts/content/builder' );
	endwhile;
	?>
</main>
<?php wp_footer(); ?>
</body>
</html>
