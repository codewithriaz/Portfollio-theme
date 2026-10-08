<?php
/**
 * Section: About.
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;

$riaz_args   = wp_parse_args( isset( $args ) ? $args : array(), array( 'id' => 'about' ) );
$riaz_points = array_values( array_filter( wp_list_pluck( riaz_mod_list( 'about_points' ), 'text' ) ) );
$riaz_name   = riaz_mod( 'about_name' );
$riaz_role   = riaz_mod( 'about_role' );
$riaz_path   = wp_unique_id( 'riaz-badge-path-' );
?>
<section class="about-section section-shell" id="<?php echo esc_attr( $riaz_args['id'] ); ?>"<?php riaz_section_attrs( 'about' ); ?>>
	<div class="about-visual" data-reveal="left">
		<div class="about-photo">
			<?php
			echo riaz_get_option_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper.
				'about_image',
				riaz_bundled_image( 'about' ),
				array(
					/* translators: %s: person name. */
					'alt'   => $riaz_name ? sprintf( __( 'Portrait of %s', 'riaz-portfolio' ), $riaz_name ) : '',
					'sizes' => '(max-width: 900px) 90vw, 440px',
				)
			);
			?>
		</div>

		<?php if ( riaz_mod_bool( 'about_badge_show' ) && riaz_mod( 'about_badge_text' ) ) : ?>
			<svg class="rotating-badge" viewBox="0 0 120 120" aria-hidden="true" focusable="false">
				<defs>
					<path id="<?php echo esc_attr( $riaz_path ); ?>" d="M60,60 m-46,0 a46,46 0 1,1 92,0 a46,46 0 1,1 -92,0"></path>
				</defs>
				<text>
					<textPath href="#<?php echo esc_attr( $riaz_path ); ?>" textLength="282" lengthAdjust="spacing"><?php echo esc_html( riaz_mod( 'about_badge_text' ) ); ?></textPath>
				</text>
			</svg>
			<span class="rotating-badge-center" aria-hidden="true"><?php riaz_icon( 'arrow-up-right', 22 ); ?></span>
		<?php endif; ?>

		<?php if ( $riaz_name || $riaz_role ) : ?>
			<div class="about-tag">
				<?php if ( $riaz_name ) : ?>
					<strong><?php echo esc_html( $riaz_name ); ?></strong>
				<?php endif; ?>
				<?php if ( $riaz_role ) : ?>
					<small><?php echo esc_html( $riaz_role ); ?></small>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>

	<div class="about-content" data-reveal="right">
		<?php if ( riaz_mod( 'about_eyebrow' ) ) : ?>
			<p class="eyebrow"><?php riaz_icon( 'sparkles', 13 ); ?> <?php echo esc_html( riaz_mod( 'about_eyebrow' ) ); ?></p>
		<?php endif; ?>
		<h2>
			<?php riaz_inline( riaz_mod( 'about_title' ) ); ?>
			<?php if ( riaz_mod( 'about_highlight' ) ) : ?>
				<span class="text-accent"><?php riaz_inline( riaz_mod( 'about_highlight' ) ); ?></span>
			<?php endif; ?>
		</h2>
		<?php if ( riaz_mod( 'about_lead' ) ) : ?>
			<div class="about-lead"><?php echo riaz_paragraphs( riaz_mod( 'about_lead' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_kses_post in helper. ?></div>
		<?php endif; ?>
		<?php if ( riaz_mod( 'about_text' ) ) : ?>
			<div class="about-copy"><?php echo riaz_paragraphs( riaz_mod( 'about_text' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_kses_post in helper. ?></div>
		<?php endif; ?>
		<?php if ( $riaz_points ) : ?>
			<ul class="principles">
				<?php foreach ( $riaz_points as $riaz_point ) : ?>
					<li><span class="check-dot" aria-hidden="true"><?php riaz_icon( 'check', 14, array( 'stroke' => 3 ) ); ?></span><?php echo esc_html( $riaz_point ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
		<div class="about-actions">
			<?php riaz_button( riaz_mod( 'about_btn_label' ), riaz_mod( 'about_btn_url' ) ); ?>
			<?php riaz_link_arrow( riaz_mod( 'about_link_label' ), riaz_mod( 'about_link_url' ) ); ?>
		</div>
	</div>
</section>
