<?php
/**
 * Section: Hero.
 *
 * The headline and portrait are never opacity-animated so the largest
 * contentful paint is not delayed.
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;

$riaz_args      = wp_parse_args( isset( $args ) ? $args : array(), array( 'id' => 'top' ) );
$riaz_eyebrow   = riaz_mod( 'hero_eyebrow' );
$riaz_title     = riaz_mod( 'hero_title' );
$riaz_highlight = riaz_mod( 'hero_highlight' );
$riaz_text      = riaz_mod( 'hero_text' );
$riaz_focus     = riaz_mod_list( 'hero_focus' );
$riaz_badge     = riaz_mod( 'hero_badge_title' );
$riaz_badge_sub = riaz_mod( 'hero_badge_text' );
$riaz_alt       = trim( $riaz_badge . ( $riaz_badge_sub ? ', ' . $riaz_badge_sub : '' ) );
?>
<section class="hero-section" id="<?php echo esc_attr( $riaz_args['id'] ); ?>"<?php riaz_section_attrs( 'hero' ); ?>>
	<div class="hero-bg" aria-hidden="true">
		<span class="hero-grid-lines"></span>
		<span class="hero-glow hero-glow-one"></span>
		<span class="hero-glow hero-glow-two"></span>
	</div>

	<div class="hero-grid section-shell">
		<div class="hero-copy">
			<?php if ( $riaz_eyebrow ) : ?>
				<p class="hero-eyebrow anim-slide">
					<span class="pulse-dot" aria-hidden="true"></span>
					<?php echo esc_html( $riaz_eyebrow ); ?>
				</p>
			<?php endif; ?>

			<h1>
				<?php riaz_inline( $riaz_title ); ?>
				<?php if ( $riaz_highlight ) : ?>
					<span class="gradient-text"><?php riaz_inline( $riaz_highlight ); ?></span>
				<?php endif; ?>
			</h1>

			<?php if ( $riaz_text ) : ?>
				<p class="hero-description"><?php riaz_inline( $riaz_text ); ?></p>
			<?php endif; ?>

			<?php if ( riaz_mod( 'hero_btn1_label' ) || riaz_mod( 'hero_btn2_label' ) ) : ?>
				<div class="hero-actions anim-slide" style="--delay:0.1s">
					<?php riaz_button( riaz_mod( 'hero_btn1_label' ), riaz_mod( 'hero_btn1_url' ) ); ?>
					<?php riaz_button( riaz_mod( 'hero_btn2_label' ), riaz_mod( 'hero_btn2_url' ), 'ghost' ); ?>
				</div>
			<?php endif; ?>

			<?php if ( $riaz_focus ) : ?>
				<ul class="hero-focus anim-slide" style="--delay:0.2s">
					<?php foreach ( $riaz_focus as $riaz_item ) : ?>
						<?php
						if ( empty( $riaz_item['text'] ) ) {
							continue; }
						?>
						<li><?php riaz_icon( isset( $riaz_item['icon'] ) ? $riaz_item['icon'] : 'sparkles', 16 ); ?><?php echo esc_html( $riaz_item['text'] ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>

		<div class="hero-portrait-wrap">
			<span class="portrait-ring portrait-ring-one" aria-hidden="true"></span>
			<span class="portrait-ring portrait-ring-two" aria-hidden="true"></span>
			<div class="hero-portrait">
				<?php
				echo riaz_get_option_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper.
					'hero_image',
					riaz_bundled_image( 'portrait' ),
					array(
						'alt'           => $riaz_alt,
						'sizes'         => '(max-width: 900px) 90vw, 46vw',
						'loading'       => 'eager',
						'fetchpriority' => 'high',
					)
				);
				?>
			</div>

			<?php if ( riaz_mod_bool( 'hero_note_show' ) && riaz_mod( 'hero_note_title' ) ) : ?>
				<div class="glass-note anim-float" style="--delay:0.5s">
					<div class="float-loop">
						<div class="glass-note-inner">
							<span class="glass-note-icon" aria-hidden="true"><?php riaz_icon( 'sparkles', 15 ); ?></span>
							<span>
								<strong><?php echo esc_html( riaz_mod( 'hero_note_title' ) ); ?></strong>
								<?php if ( riaz_mod( 'hero_note_text' ) ) : ?>
									<small><?php echo esc_html( riaz_mod( 'hero_note_text' ) ); ?></small>
								<?php endif; ?>
							</span>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( riaz_mod_bool( 'hero_badge_show' ) && $riaz_badge ) : ?>
				<div class="glass-badge anim-float" style="--delay:0.8s">
					<div class="float-loop">
						<strong><?php echo esc_html( $riaz_badge ); ?></strong>
						<?php if ( $riaz_badge_sub ) : ?>
							<small><?php echo esc_html( $riaz_badge_sub ); ?></small>
						<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
