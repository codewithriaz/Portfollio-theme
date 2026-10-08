<?php
/**
 * Section: Case studies.
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;

$riaz_args  = wp_parse_args( isset( $args ) ? $args : array(), array( 'id' => 'case-studies' ) );
$riaz_query = riaz_section_query( 'riaz_case_study', riaz_mod( 'cases_count' ) );

if ( ! $riaz_query->have_posts() ) {
	riaz_admin_placeholder(
		__( 'Case studies', 'riaz-portfolio' ),
		__( 'Publish a case study to show this section.', 'riaz-portfolio' ),
		admin_url( 'post-new.php?post_type=riaz_case_study' ),
		__( 'Add a case study', 'riaz-portfolio' )
	);
	return;
}
$riaz_archive = get_post_type_archive_link( 'riaz_case_study' );
?>
<section class="case-studies-section" id="<?php echo esc_attr( $riaz_args['id'] ); ?>"<?php riaz_section_attrs( 'case_studies' ); ?>>
	<div class="section-shell">
		<div class="section-topline">
			<div data-reveal><?php riaz_section_heading( riaz_heading_args( 'cases' ) ); ?></div>
			<?php if ( $riaz_archive ) : ?>
				<div data-reveal style="--reveal-delay:0.1s"><?php riaz_link_arrow( riaz_mod( 'cases_link_label' ), $riaz_archive ); ?></div>
			<?php endif; ?>
		</div>
		<div class="cases-grid<?php echo 1 === $riaz_query->post_count ? ' is-single' : ''; ?>" data-stagger style="--cases-cols:<?php echo esc_attr( min( 3, $riaz_query->post_count ) ); ?>">
			<?php
			while ( $riaz_query->have_posts() ) {
				$riaz_query->the_post();
				get_template_part( 'template-parts/cards/case-study' );
			}
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>
