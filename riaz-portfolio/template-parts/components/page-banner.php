<?php
/**
 * Component: dark page banner with breadcrumbs, title and description.
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;

$riaz_banner = riaz_banner_data();
?>
<header class="page-banner<?php echo $riaz_banner['centered'] ? ' is-centered' : ''; ?>">
	<div class="hero-bg" aria-hidden="true">
		<span class="hero-grid-lines"></span>
		<span class="hero-glow hero-glow-one"></span>
		<span class="hero-glow hero-glow-two"></span>
	</div>
	<div class="section-shell page-banner-inner">
		<?php riaz_breadcrumbs(); ?>
		<?php if ( $riaz_banner['eyebrow'] ) : ?>
			<p class="hero-eyebrow anim-slide"><span class="pulse-dot" aria-hidden="true"></span><?php echo esc_html( $riaz_banner['eyebrow'] ); ?></p>
		<?php endif; ?>
		<h1><?php echo esc_html( $riaz_banner['title'] ); ?></h1>
		<?php if ( $riaz_banner['text'] ) : ?>
			<div class="page-banner-text"><?php echo wp_kses_post( wpautop( $riaz_banner['text'] ) ); ?></div>
		<?php endif; ?>
		<?php if ( $riaz_banner['meta'] ) : ?>
			<ul class="page-banner-meta">
				<li><?php echo get_avatar( get_the_author_meta( 'ID' ), 28, '', '' ); ?><?php the_author(); ?></li>
				<li><?php riaz_icon( 'calendar', 15 ); ?><time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time></li>
				<li><?php riaz_icon( 'clock', 15 ); ?>
					<?php
					/* translators: %d: minutes. */
					echo esc_html( sprintf( _n( '%d min read', '%d min read', riaz_reading_time(), 'riaz-portfolio' ), riaz_reading_time() ) );
					?>
				</li>
			</ul>
		<?php endif; ?>
		<?php if ( $riaz_banner['search'] ) : ?>
			<?php get_search_form(); ?>
		<?php endif; ?>
	</div>
</header>
