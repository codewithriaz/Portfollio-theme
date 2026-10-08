<?php
/**
 * Single entries. Post types with their own layout use
 * template-parts/content/single-{post_type}.php.
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<main id="main" class="site-main">
		<?php
		get_template_part( 'template-parts/content/single', get_post_type() );

		if ( in_array( get_post_type(), array( 'riaz_portfolio', 'riaz_case_study', 'riaz_service' ), true ) && riaz_mod_bool( 'cta_on_singles' ) ) {
			riaz_render_section( 'cta', array( 'context' => 'single' ) );
		}
		?>
	</main>
	<?php
endwhile;

get_footer();
