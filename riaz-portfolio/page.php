<?php
/**
 * Default page template.
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
			<?php
			if ( riaz_show_page_banner() ) {
				get_template_part( 'template-parts/components/page-banner' );
			}
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

get_footer();
