<?php
/**
 * Section: Call to action banner.
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;

$riaz_args  = wp_parse_args( isset( $args ) ? $args : array(), array( 'id' => 'contact' ) );
$riaz_photo = '';
if ( riaz_mod_bool( 'cta_image_show' ) ) {
	$riaz_photo = riaz_get_option_image(
		absint( riaz_mod( 'cta_image' ) ) ? 'cta_image' : 'hero_image',
		riaz_bundled_image( 'portrait' ),
		array(
			'alt'   => '',
			'sizes' => '(max-width: 900px) 70vw, 420px',
		)
	);
}
?>
<section class="cta-section section-shell" id="<?php echo esc_attr( $riaz_args['id'] ); ?>"<?php riaz_section_attrs( 'cta' ); ?>>
	<div class="cta-card<?php echo $riaz_photo ? '' : ' no-photo'; ?>" data-reveal>
		<div class="cta-bg" aria-hidden="true">
			<span class="hero-grid-lines"></span>
			<span class="hero-glow hero-glow-one"></span>
		</div>
		<div class="cta-content">
			<?php if ( riaz_mod( 'cta_eyebrow' ) ) : ?>
				<p class="hero-eyebrow"><span class="pulse-dot" aria-hidden="true"></span><?php echo esc_html( riaz_mod( 'cta_eyebrow' ) ); ?></p>
			<?php endif; ?>
			<h2>
				<?php riaz_inline( riaz_mod( 'cta_title' ) ); ?>
				<?php if ( riaz_mod( 'cta_highlight' ) ) : ?>
					<span class="gradient-text"><?php riaz_inline( riaz_mod( 'cta_highlight' ) ); ?></span>
				<?php endif; ?>
			</h2>
			<?php if ( riaz_mod( 'cta_text' ) ) : ?>
				<p class="cta-text"><?php riaz_inline( riaz_mod( 'cta_text' ) ); ?></p>
			<?php endif; ?>
			<div class="cta-actions">
				<?php riaz_button( riaz_mod( 'cta_btn_label' ), riaz_mod( 'cta_btn_url' ), 'light' ); ?>
				<?php riaz_button( riaz_mod( 'cta_btn2_label' ), riaz_mod( 'cta_btn2_url' ), 'ghost' ); ?>
			</div>
		</div>
		<?php if ( $riaz_photo ) : ?>
			<div class="cta-photo" aria-hidden="true"><?php echo $riaz_photo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper. ?></div>
		<?php endif; ?>
	</div>
</section>
