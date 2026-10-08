<?php
/**
 * Template Name: Homepage Sections
 * Template Post Type: page
 *
 * Renders the Customizer homepage sections on any page, followed by the
 * page's own content (if any).
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="site-main">
	<?php riaz_render_homepage_sections(); ?>
	<?php
	while ( have_posts() ) :
		the_post();
		if ( '' !== trim( get_the_content() ) ) {
			get_template_part( 'template-parts/content/page-body' );
		}
	endwhile;
	?>
</main>
<?php
get_footer();
