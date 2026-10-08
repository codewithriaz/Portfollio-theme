<?php
/**
 * Component: horizontal scroll-snap carousel with prev/next buttons.
 *
 * Expects $args: 'label' (accessible name), 'slides' (pre-rendered HTML).
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;

$riaz_args = wp_parse_args(
	isset( $args ) ? $args : array(),
	array(
		'label'  => '',
		'slides' => '',
	)
);
?>
<div class="carousel" data-carousel>
	<div class="carousel-track" role="region" aria-label="<?php echo esc_attr( $riaz_args['label'] ); ?>" tabindex="0" data-carousel-track>
		<div class="carousel-inner" data-stagger>
			<?php echo $riaz_args['slides']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- slides are escaped when rendered. ?>
		</div>
	</div>
	<div class="carousel-controls" hidden data-carousel-controls>
		<button type="button" class="carousel-button" data-carousel-prev aria-label="<?php esc_attr_e( 'Previous', 'riaz-portfolio' ); ?>"><?php riaz_icon( 'chevron-left', 20 ); ?></button>
		<button type="button" class="carousel-button" data-carousel-next aria-label="<?php esc_attr_e( 'Next', 'riaz-portfolio' ); ?>"><?php riaz_icon( 'chevron-right', 20 ); ?></button>
	</div>
</div>
