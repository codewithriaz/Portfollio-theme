<?php
/**
 * Empty state for listings.
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="no-results">
	<h2><?php esc_html_e( 'Nothing found', 'riaz-portfolio' ); ?></h2>
	<?php if ( is_search() ) : ?>
		<p><?php esc_html_e( 'Nothing matched your search. Try different keywords.', 'riaz-portfolio' ); ?></p>
	<?php elseif ( is_home() && current_user_can( 'publish_posts' ) ) : ?>
		<p>
			<?php
			printf(
				/* translators: %s: new post URL. */
				wp_kses_post( __( 'Ready to publish your first post? <a href="%s">Get started here</a>.', 'riaz-portfolio' ) ),
				esc_url( admin_url( 'post-new.php' ) )
			);
			?>
		</p>
	<?php else : ?>
		<p><?php esc_html_e( 'There is nothing here yet. Try searching instead.', 'riaz-portfolio' ); ?></p>
	<?php endif; ?>
	<?php get_search_form(); ?>
</section>
