<?php
/**
 * Card: Case study (used in the loop).
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;

$riaz_id      = get_the_ID();
$riaz_meta    = array_filter( array( riaz_meta( $riaz_id, 'client' ), riaz_meta( $riaz_id, 'industry' ) ) );
$riaz_metrics = riaz_case_metrics( $riaz_id, 2 );
?>
<article class="case-card lift" data-reveal-item>
	<?php riaz_card_media( 'riaz-card' ); ?>
	<div class="case-body">
		<?php if ( $riaz_meta ) : ?>
			<p class="case-meta"><?php echo esc_html( implode( ' · ', $riaz_meta ) ); ?></p>
		<?php endif; ?>
		<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<?php if ( has_excerpt() || get_the_content() ) : ?>
			<p><?php echo esc_html( wp_strip_all_tags( get_the_excerpt() ) ); ?></p>
		<?php endif; ?>
		<?php if ( $riaz_metrics ) : ?>
			<dl class="metric-list">
				<?php foreach ( $riaz_metrics as $riaz_metric ) : ?>
					<div><dt><?php echo esc_html( $riaz_metric['label'] ); ?></dt><dd class="text-accent"><?php echo esc_html( $riaz_metric['value'] ); ?></dd></div>
				<?php endforeach; ?>
			</dl>
		<?php endif; ?>
		<a class="link-arrow" href="<?php the_permalink(); ?>">
			<?php esc_html_e( 'Read case study', 'riaz-portfolio' ); ?>
			<span class="screen-reader-text"><?php the_title(); ?></span>
			<?php riaz_icon( 'arrow-up-right', 16 ); ?>
		</a>
	</div>
</article>
