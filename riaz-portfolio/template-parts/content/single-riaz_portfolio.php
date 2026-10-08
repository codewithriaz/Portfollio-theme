<?php
/**
 * Single portfolio project.
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;

$riaz_id    = get_the_ID();
$riaz_terms = get_the_terms( $riaz_id, 'riaz_portfolio_cat' );
$riaz_facts = array_filter(
	array(
		__( 'Client', 'riaz-portfolio' )   => riaz_meta( $riaz_id, 'client' ),
		__( 'Services', 'riaz-portfolio' ) => riaz_meta( $riaz_id, 'role' ),
		__( 'Year', 'riaz-portfolio' )     => riaz_meta( $riaz_id, 'year' ),
		__( 'Category', 'riaz-portfolio' ) => ( $riaz_terms && ! is_wp_error( $riaz_terms ) ) ? implode( ', ', wp_list_pluck( $riaz_terms, 'name' ) ) : '',
	)
);
$riaz_url   = riaz_meta( $riaz_id, 'project_url' );
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
			<?php if ( $riaz_facts || $riaz_url ) : ?>
				<ul class="project-facts" data-stagger>
					<?php foreach ( $riaz_facts as $riaz_label => $riaz_value ) : ?>
						<li data-reveal-item><small><?php echo esc_html( $riaz_label ); ?></small><strong><?php echo esc_html( $riaz_value ); ?></strong></li>
					<?php endforeach; ?>
					<?php if ( $riaz_url ) : ?>
						<li data-reveal-item><small><?php esc_html_e( 'Live project', 'riaz-portfolio' ); ?></small><a href="<?php echo esc_url( $riaz_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Visit site', 'riaz-portfolio' ); ?> <?php riaz_icon( 'external-link', 15 ); ?></a></li>
					<?php endif; ?>
				</ul>
			<?php endif; ?>

			<div class="entry-content<?php echo riaz_is_builder_content( $riaz_id ) ? ' is-builder' : ''; ?>"><?php the_content(); ?></div>

			<?php
			the_post_navigation(
				array(
					'prev_text' => '<span class="nav-subtitle">' . esc_html__( 'Previous project', 'riaz-portfolio' ) . '</span><span class="nav-title">%title</span>',
					'next_text' => '<span class="nav-subtitle">' . esc_html__( 'Next project', 'riaz-portfolio' ) . '</span><span class="nav-title">%title</span>',
				)
			);
			get_template_part( 'template-parts/content/related' );
			?>
		</div>
	</div>
</article>
