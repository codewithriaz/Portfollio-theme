<?php
/**
 * Card: Service (used in the loop).
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;

$riaz_args     = wp_parse_args( isset( $args ) ? $args : array(), array( 'index' => 0 ) );
$riaz_id       = get_the_ID();
$riaz_featured = (bool) riaz_meta( $riaz_id, 'featured' );
$riaz_label    = riaz_mod( 'services_card_label' );
$riaz_link     = riaz_service_link( $riaz_id );
?>
<article class="service-card spotlight lift<?php echo $riaz_featured ? ' service-card-featured' : ''; ?>" data-reveal-item>
	<div class="service-card-top">
		<span class="icon-tile" aria-hidden="true"><?php riaz_icon( riaz_meta( $riaz_id, 'icon', 'sparkles' ), 22 ); ?></span>
		<span class="service-index" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', (int) $riaz_args['index'] + 1 ) ); ?></span>
	</div>
	<h3><?php the_title(); ?></h3>
	<?php if ( has_excerpt() || get_the_content() ) : ?>
		<p><?php echo esc_html( wp_strip_all_tags( get_the_excerpt() ) ); ?></p>
	<?php endif; ?>
	<?php if ( $riaz_label && $riaz_link ) : ?>
		<a class="service-link" href="<?php echo esc_url( $riaz_link ); ?>">
			<?php echo esc_html( $riaz_label ); ?>
			<span class="screen-reader-text">
				<?php
				/* translators: %s: service name. */
				echo esc_html( sprintf( __( 'about %s', 'riaz-portfolio' ), get_the_title() ) );
				?>
			</span>
			<?php riaz_icon( 'arrow-right', 15 ); ?>
		</a>
	<?php endif; ?>
</article>
