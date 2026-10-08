<?php
/**
 * Section: Approach / process timeline.
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;

$riaz_args  = wp_parse_args( isset( $args ) ? $args : array(), array( 'id' => 'approach' ) );
$riaz_steps = array_values(
	array_filter(
		riaz_mod_list( 'approach_steps' ),
		static function ( $row ) {
			return ! empty( $row['title'] );
		}
	)
);

if ( ! $riaz_steps ) {
	return;
}
?>
<section class="approach-section" id="<?php echo esc_attr( $riaz_args['id'] ); ?>"<?php riaz_section_attrs( 'approach' ); ?>>
	<div class="approach-bg" aria-hidden="true"></div>
	<div class="section-shell">
		<div data-reveal>
			<?php
			riaz_section_heading(
				riaz_heading_args(
					'approach',
					array(
						'centered' => true,
						'dark'     => true,
					)
				)
			);
			?>
		</div>
		<div class="approach-timeline" style="--steps:<?php echo esc_attr( min( 6, count( $riaz_steps ) ) ); ?>">
			<div class="timeline-line" aria-hidden="true" data-timeline><span></span></div>
			<ol class="approach-steps" data-stagger>
				<?php foreach ( $riaz_steps as $riaz_index => $riaz_step ) : ?>
					<li class="approach-step lift" data-reveal-item>
						<span class="step-dot" aria-hidden="true"><?php riaz_icon( isset( $riaz_step['icon'] ) ? $riaz_step['icon'] : 'sparkles', 18 ); ?></span>
						<span class="step-number">
							<?php
							/* translators: %s: step number, e.g. 01. */
							echo esc_html( sprintf( __( 'Step %s', 'riaz-portfolio' ), sprintf( '%02d', $riaz_index + 1 ) ) );
							?>
						</span>
						<h3><?php echo esc_html( $riaz_step['title'] ); ?></h3>
						<?php if ( ! empty( $riaz_step['text'] ) ) : ?>
							<p><?php echo esc_html( $riaz_step['text'] ); ?></p>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</div>
</section>
