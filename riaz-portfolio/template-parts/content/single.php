<?php
/**
 * Single blog post.
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;

$riaz_banner  = riaz_show_page_banner();
$riaz_sidebar = riaz_has_sidebar();
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php
	if ( $riaz_banner ) {
		get_template_part( 'template-parts/components/page-banner' );
	}
	get_template_part( 'template-parts/content/featured-image' );
	?>
	<div class="content-area">
		<div class="section-shell">
			<div class="content-layout<?php echo $riaz_sidebar ? ' has-sidebar' : ''; ?>">
				<div>
					<?php if ( ! $riaz_banner ) : ?>
						<header class="entry-header-plain">
							<h1><?php the_title(); ?></h1>
							<?php riaz_post_meta( true ); ?>
						</header>
					<?php endif; ?>

					<?php if ( riaz_is_builder_content( get_the_ID() ) ) : ?>
						<div class="entry-content is-builder"><?php the_content(); ?></div>
					<?php else : ?>
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
					<?php endif; ?>

					<footer class="entry-footer">
						<?php
						$riaz_tags = get_the_tags();
						if ( $riaz_tags ) {
							echo '<ul class="chip-list" aria-label="' . esc_attr__( 'Tags', 'riaz-portfolio' ) . '">';
							foreach ( $riaz_tags as $riaz_tag ) {
								printf( '<li><a href="%1$s" rel="tag">#%2$s</a></li>', esc_url( get_tag_link( $riaz_tag ) ), esc_html( $riaz_tag->name ) );
							}
							echo '</ul>';
						}
						if ( riaz_mod_bool( 'blog_share' ) ) {
							riaz_share_links();
						}
						?>
					</footer>

					<?php if ( riaz_mod_bool( 'blog_author_box' ) && get_the_author_meta( 'description' ) ) : ?>
						<section class="author-box" aria-label="<?php esc_attr_e( 'About the author', 'riaz-portfolio' ); ?>">
							<?php echo get_avatar( get_the_author_meta( 'ID' ), 72 ); ?>
							<div>
								<h2><?php the_author(); ?></h2>
								<p><?php echo esc_html( get_the_author_meta( 'description' ) ); ?></p>
							</div>
						</section>
					<?php endif; ?>

					<?php
					the_post_navigation(
						array(
							'prev_text' => '<span class="nav-subtitle">' . esc_html__( 'Previous', 'riaz-portfolio' ) . '</span><span class="nav-title">%title</span>',
							'next_text' => '<span class="nav-subtitle">' . esc_html__( 'Next', 'riaz-portfolio' ) . '</span><span class="nav-title">%title</span>',
						)
					);

					if ( comments_open() || get_comments_number() ) {
						comments_template();
					}
					?>
				</div>
				<?php
				if ( $riaz_sidebar ) {
					get_sidebar();
				}
				?>
			</div>

			<?php get_template_part( 'template-parts/content/related' ); ?>
		</div>
	</div>
</article>
