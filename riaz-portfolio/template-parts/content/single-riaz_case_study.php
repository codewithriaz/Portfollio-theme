<?php
/**
 * Single case study.
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;

$riaz_id      = get_the_ID();
$riaz_facts   = array_filter(
	array(
		__( 'Client', 'riaz-portfolio' )   => riaz_meta( $riaz_id, 'client' ),
		__( 'Industry', 'riaz-portfolio' ) => riaz_meta( $riaz_id, 'industry' ),
		__( 'Timeline', 'riaz-portfolio' ) => riaz_meta( $riaz_id, 'duration' ),
	)
);
$riaz_metrics = riaz_case_metrics( $riaz_id, 3 );
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
			<?php if ( $riaz_facts ) : ?>
				<ul class="project-facts" data-stagger>
					<?php foreach ( $riaz_facts as $riaz_label => $riaz_value ) : ?>
						<li data-reveal-item><small><?php echo esc_html( $riaz_label ); ?></small><strong><?php echo esc_html( $riaz_value ); ?></strong></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<?php if ( $riaz_metrics ) : ?>
				<dl class="metric-band" data-stagger aria-label="<?php esc_attr_e( 'Results', 'riaz-portfolio' ); ?>">
					<?php foreach ( $riaz_metrics as $riaz_metric ) : ?>
						<div data-reveal-item><dt><?php echo esc_html( $riaz_metric['label'] ); ?></dt><dd class="text-accent"><?php echo esc_html( $riaz_metric['value'] ); ?></dd></div>
					<?php endforeach; ?>
				</dl>
			<?php endif; ?>

			<div class="entry-content<?php echo riaz_is_builder_content( $riaz_id ) ? ' is-builder' : ''; ?>"><?php the_content(); ?></div>

			<?php
			the_post_navigation(
				array(
					'prev_text' => '<span class="nav-subtitle">' . esc_html__( 'Previous case study', 'riaz-portfolio' ) . '</span><span class="nav-title">%title</span>',
					'next_text' => '<span class="nav-subtitle">' . esc_html__( 'Next case study', 'riaz-portfolio' ) . '</span><span class="nav-title">%title</span>',
				)
			);
			get_template_part( 'template-parts/content/related' );
			?>
		</div>
	</div>
</article>
