<?php
/**
 * Section: "Who I help" marquee.
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;

$riaz_args   = wp_parse_args( isset( $args ) ? $args : array(), array( 'id' => 'marquee' ) );
$riaz_labels = array_values( array_filter( wp_list_pluck( riaz_mod_list( 'marquee_items' ), 'text' ) ) );

if ( ! $riaz_labels ) {
	return;
}

$riaz_label    = riaz_mod( 'marquee_label' );
$riaz_unique   = count( $riaz_labels );
$riaz_track    = $riaz_labels;
$riaz_label_id = wp_unique_id( 'riaz-marquee-label-' );

// Repeat short lists so one track is always wider than the viewport.
$riaz_repeat = (int) ceil( 10 / $riaz_unique );
for ( $riaz_i = 1; $riaz_i < $riaz_repeat; $riaz_i++ ) {
	$riaz_track = array_merge( $riaz_track, $riaz_labels );
}
?>
<section class="marquee-section" id="<?php echo esc_attr( $riaz_args['id'] ); ?>"<?php echo $riaz_label ? ' aria-labelledby="' . esc_attr( $riaz_label_id ) . '"' : ''; ?><?php riaz_section_attrs( 'marquee' ); ?>>
	<?php if ( $riaz_label ) : ?>
		<p class="marquee-label" id="<?php echo esc_attr( $riaz_label_id ); ?>"><?php echo esc_html( $riaz_label ); ?></p>
	<?php endif; ?>
	<div class="marquee" style="--marquee-duration:<?php echo esc_attr( absint( riaz_mod( 'marquee_speed' ) ) ); ?>s">
		<?php for ( $riaz_copy = 0; $riaz_copy < 2; $riaz_copy++ ) : ?>
			<ul class="marquee-track"<?php echo $riaz_copy ? ' aria-hidden="true"' : ''; ?>>
				<?php foreach ( $riaz_track as $riaz_index => $riaz_text ) : ?>
					<li<?php echo ( ! $riaz_copy && $riaz_index >= $riaz_unique ) ? ' aria-hidden="true"' : ''; ?>><?php riaz_icon( 'sparkles', 14 ); ?> <?php echo esc_html( $riaz_text ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endfor; ?>
	</div>
</section>
