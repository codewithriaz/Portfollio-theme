<?php
/**
 * Front page.
 *
 * Shows the Customizer-driven homepage sections, unless the static front page
 * has its own content (blocks, Elementor or Divi), which is then shown instead.
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( riaz_front_page_uses_sections() ) :
	?>
	<main id="main" class="site-main">
		<?php riaz_render_homepage_sections(); ?>
	</main>
	<?php
elseif ( 'page' === get_option( 'show_on_front' ) ) :
	while ( have_posts() ) :
		the_post();
		?>
		<main id="main" class="site-main">
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<?php
				if ( riaz_is_builder_content( get_the_ID() ) ) {
					get_template_part( 'template-parts/content/builder' );
				} else {
					get_template_part( 'template-parts/content/page-body' );
				}
				?>
			</article>
		</main>
		<?php
	endwhile;
else :
	get_template_part( 'template-parts/content/archive' );
endif;

get_footer();
