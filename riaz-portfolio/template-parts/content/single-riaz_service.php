<?php
/**
 * Single service.
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php
	if ( riaz_show_page_banner() ) {
		get_template_part( 'template-parts/components/page-banner' );
	}
	get_template_part( 'template-parts/content/featured-image' );
	?>
	<div class="content-area">
		<div class="section-shell">
			<div class="entry-content<?php echo riaz_is_builder_content( get_the_ID() ) ? ' is-builder' : ''; ?>"><?php the_content(); ?></div>
			<?php get_template_part( 'template-parts/content/related' ); ?>
		</div>
	</div>
</article>
