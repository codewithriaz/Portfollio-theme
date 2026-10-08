<?php
/**
 * Standard page body: readable content width with wide/full alignments.
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="content-area">
	<div class="section-shell">
		<div class="entry-content">
			<?php
			the_content();
			wp_link_pages(
				array(
					'before' => '<nav class="page-links" aria-label="' . esc_attr__( 'Page', 'riaz-portfolio' ) . '">',
					'after'  => '</nav>',
				)
			);
			?>
		</div>
		<?php
		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}
		?>
	</div>
</div>
