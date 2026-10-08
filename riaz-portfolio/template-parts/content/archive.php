<?php
/**
 * Listing layout shared by the blog index, archives and search results.
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;

if ( is_post_type_archive( 'riaz_portfolio' ) || is_tax( 'riaz_portfolio_cat' ) ) {
	$riaz_card = 'project';
	$riaz_grid = 'projects-grid';
} elseif ( is_post_type_archive( 'riaz_case_study' ) ) {
	$riaz_card = 'case-study';
	$riaz_grid = 'cases-grid';
} elseif ( is_post_type_archive( 'riaz_service' ) ) {
	$riaz_card = 'service';
	$riaz_grid = 'services-grid';
} else {
	$riaz_card = 'post';
	$riaz_grid = 'posts-grid' . ( 'list' === riaz_mod( 'blog_layout' ) && ! is_search() ? ' is-list' : '' );
}

$riaz_sidebar = 'post' === $riaz_card && riaz_has_sidebar();
$riaz_offset  = max( 0, ( (int) get_query_var( 'paged', 1 ) - 1 ) ) * (int) get_query_var( 'posts_per_page', 10 );
?>
<main id="main" class="site-main">
	<?php
	if ( riaz_show_page_banner() ) {
		get_template_part( 'template-parts/components/page-banner' );
	} else {
		echo '<h1 class="screen-reader-text">' . esc_html( riaz_banner_data()['title'] ) . '</h1>';
	}
	?>
	<div class="content-area">
		<div class="section-shell">
			<?php
			if ( 'project' === $riaz_card ) {
				riaz_portfolio_term_filter();
			}
			?>
			<div class="content-layout<?php echo $riaz_sidebar ? ' has-sidebar' : ''; ?>">
				<div>
					<?php if ( have_posts() ) : ?>
						<div class="<?php echo esc_attr( $riaz_grid ); ?>" data-stagger<?php echo 'service' === $riaz_card ? ' data-spotlight' : ''; ?>>
							<?php
							$riaz_index = $riaz_offset;
							while ( have_posts() ) {
								the_post();
								get_template_part( 'template-parts/cards/' . $riaz_card, null, array( 'index' => $riaz_index++ ) );
							}
							?>
						</div>
						<?php riaz_pagination(); ?>
					<?php else : ?>
						<?php get_template_part( 'template-parts/content/none' ); ?>
					<?php endif; ?>
				</div>
				<?php
				if ( $riaz_sidebar ) {
					get_sidebar();
				}
				?>
			</div>
		</div>
	</div>
</main>
