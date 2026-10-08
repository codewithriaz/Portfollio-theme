<?php
/**
 * Section: Portfolio grid with category filter.
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;

$riaz_args  = wp_parse_args( isset( $args ) ? $args : array(), array( 'id' => 'portfolio' ) );
$riaz_query = riaz_section_query( 'riaz_portfolio', riaz_mod( 'portfolio_count' ) );

if ( ! $riaz_query->have_posts() ) {
	riaz_admin_placeholder(
		__( 'Portfolio', 'riaz-portfolio' ),
		__( 'Publish a project to show this section.', 'riaz-portfolio' ),
		admin_url( 'post-new.php?post_type=riaz_portfolio' ),
		__( 'Add a project', 'riaz-portfolio' )
	);
	return;
}

$riaz_terms = array();
foreach ( $riaz_query->posts as $riaz_post ) {
	$riaz_post_terms = get_the_terms( $riaz_post, 'riaz_portfolio_cat' );
	if ( $riaz_post_terms && ! is_wp_error( $riaz_post_terms ) ) {
		foreach ( $riaz_post_terms as $riaz_term ) {
			$riaz_terms[ $riaz_term->slug ] = $riaz_term->name;
		}
	}
}
$riaz_archive = get_post_type_archive_link( 'riaz_portfolio' );
?>
<section class="portfolio-section" id="<?php echo esc_attr( $riaz_args['id'] ); ?>"<?php riaz_section_attrs( 'portfolio' ); ?>>
	<div class="section-shell">
		<div class="section-topline">
			<div data-reveal><?php riaz_section_heading( riaz_heading_args( 'portfolio' ) ); ?></div>
			<?php if ( $riaz_archive ) : ?>
				<div data-reveal style="--reveal-delay:0.1s"><?php riaz_link_arrow( riaz_mod( 'portfolio_link_label' ), $riaz_archive ); ?></div>
			<?php endif; ?>
		</div>

		<?php if ( riaz_mod_bool( 'portfolio_filters' ) && count( $riaz_terms ) > 1 ) : ?>
			<div class="project-filters" role="group" aria-label="<?php esc_attr_e( 'Filter projects by category', 'riaz-portfolio' ); ?>" data-filter-group>
				<button type="button" aria-pressed="true" data-filter="*"><?php esc_html_e( 'All', 'riaz-portfolio' ); ?></button>
				<?php foreach ( $riaz_terms as $riaz_slug => $riaz_name ) : ?>
					<button type="button" aria-pressed="false" data-filter="<?php echo esc_attr( $riaz_slug ); ?>"><?php echo esc_html( $riaz_name ); ?></button>
				<?php endforeach; ?>
			</div>
			<p class="screen-reader-text" aria-live="polite" data-filter-status data-template="<?php /* translators: %d: number of projects. */ esc_attr_e( '%d projects shown', 'riaz-portfolio' ); ?>"></p>
		<?php endif; ?>

		<div class="projects-grid" data-stagger data-filter-grid>
			<?php
			while ( $riaz_query->have_posts() ) {
				$riaz_query->the_post();
				get_template_part( 'template-parts/cards/project' );
			}
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>
