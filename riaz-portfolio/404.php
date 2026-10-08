<?php
/**
 * 404 page.
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="main" class="site-main">
	<section class="page-banner is-centered is-404">
		<div class="hero-bg" aria-hidden="true">
			<span class="hero-grid-lines"></span>
			<span class="hero-glow hero-glow-one"></span>
			<span class="hero-glow hero-glow-two"></span>
		</div>
		<div class="section-shell page-banner-inner">
			<p class="error-code" aria-hidden="true">404</p>
			<h1><?php esc_html_e( 'This page wandered off', 'riaz-portfolio' ); ?></h1>
			<p class="page-banner-text"><?php esc_html_e( 'The page you are looking for may have moved or no longer exists. Try a search, or head back to the homepage.', 'riaz-portfolio' ); ?></p>
			<?php get_search_form(); ?>
			<div class="error-actions">
				<?php riaz_button( __( 'Back to home', 'riaz-portfolio' ), home_url( '/' ), 'light' ); ?>
				<?php riaz_button( riaz_mod( 'header_cta_label' ), home_url( '/' ) . ( 0 === strpos( (string) riaz_mod( 'header_cta_url' ), '#' ) ? riaz_mod( 'header_cta_url' ) : '' ), 'ghost' ); ?>
			</div>
		</div>
	</section>
</main>
<?php
get_footer();
