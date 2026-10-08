<?php
/**
 * Template Name: Full Width (no title)
 * Template Post Type: page, post, riaz_portfolio, riaz_case_study, riaz_service
 *
 * Edge-to-edge canvas with the site header and footer. Ideal for Elementor,
 * Divi or block layouts built from "Theme Section" blocks.
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<main id="main" class="site-main">
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<?php get_template_part( 'template-parts/content/builder' ); ?>
		</article>
	</main>
	<?php
endwhile;

get_footer();
