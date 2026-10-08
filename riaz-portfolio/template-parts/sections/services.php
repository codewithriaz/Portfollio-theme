<?php
/**
 * Section: Services grid with spotlight cards.
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;

$riaz_args  = wp_parse_args( isset( $args ) ? $args : array(), array( 'id' => 'services' ) );
$riaz_query = riaz_section_query( 'riaz_service', riaz_mod( 'services_count' ) );

if ( ! $riaz_query->have_posts() ) {
	riaz_admin_placeholder(
		__( 'Services', 'riaz-portfolio' ),
		__( 'Add your first service to show this section, or import the starter content.', 'riaz-portfolio' ),
		admin_url( 'post-new.php?post_type=riaz_service' ),
		__( 'Add a service', 'riaz-portfolio' )
	);
	return;
}
?>
<section class="services-section" id="<?php echo esc_attr( $riaz_args['id'] ); ?>"<?php riaz_section_attrs( 'services' ); ?>>
	<div class="section-shell">
		<div class="section-topline">
			<div data-reveal><?php riaz_section_heading( riaz_heading_args( 'services' ) ); ?></div>
			<div data-reveal style="--reveal-delay:0.1s"><?php riaz_link_arrow( riaz_mod( 'services_link_label' ), riaz_mod( 'services_link_url' ) ); ?></div>
		</div>
		<div class="services-grid" data-stagger data-spotlight>
			<?php
			$riaz_index = 0;
			while ( $riaz_query->have_posts() ) {
				$riaz_query->the_post();
				get_template_part( 'template-parts/cards/service', null, array( 'index' => $riaz_index++ ) );
			}
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>
