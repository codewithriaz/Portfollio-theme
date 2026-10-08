<?php
/**
 * Site footer: brand, two link columns, contact column and wordmark.
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;

$riaz_email   = riaz_mod( 'contact_email' );
$riaz_phone   = riaz_mod( 'contact_phone' );
$riaz_address = riaz_mod( 'contact_address' );
$riaz_hours   = riaz_mod( 'contact_hours' );
$riaz_brand   = riaz_brand_name();
$riaz_mark    = trim( (string) riaz_mod( 'footer_wordmark_text' ) );
?>
<footer id="colophon" class="site-footer">
	<div class="section-shell">
		<div class="footer-grid">
			<div class="footer-brand-col">
				<a class="footer-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php echo esc_html( $riaz_brand ); ?><span aria-hidden="true">.</span></a>
				<?php if ( riaz_mod( 'footer_about' ) ) : ?>
					<p><?php riaz_inline( riaz_mod( 'footer_about' ) ); ?></p>
				<?php endif; ?>
				<?php riaz_social_list(); ?>
			</div>

			<div>
				<h2><?php echo esc_html( riaz_mod( 'footer_col1_title' ) ); ?></h2>
				<?php riaz_footer_menu( 'footer_col1' ); ?>
			</div>

			<div>
				<h2><?php echo esc_html( riaz_mod( 'footer_col2_title' ) ); ?></h2>
				<?php riaz_footer_menu( 'footer_col2' ); ?>
			</div>

			<div>
				<h2><?php echo esc_html( riaz_mod( 'footer_col3_title' ) ); ?></h2>
				<ul class="footer-contact">
					<?php if ( $riaz_email ) : ?>
						<li><?php riaz_icon( 'mail', 16 ); ?><a href="<?php echo esc_url( 'mailto:' . antispambot( $riaz_email ) ); ?>"><?php echo esc_html( antispambot( $riaz_email ) ); ?></a></li>
					<?php endif; ?>
					<?php if ( $riaz_phone ) : ?>
						<li><?php riaz_icon( 'phone', 16 ); ?><a href="<?php echo esc_url( 'tel:' . preg_replace( '/[^0-9+]/', '', $riaz_phone ) ); ?>"><?php echo esc_html( $riaz_phone ); ?></a></li>
					<?php endif; ?>
					<?php if ( $riaz_address ) : ?>
						<li><?php riaz_icon( 'map-pin', 16 ); ?><span><?php echo nl2br( esc_html( wp_strip_all_tags( $riaz_address ) ) ); ?></span></li>
					<?php endif; ?>
					<?php if ( $riaz_hours ) : ?>
						<li><?php riaz_icon( 'clock', 16 ); ?><span><?php echo esc_html( $riaz_hours ); ?></span></li>
					<?php endif; ?>
					<?php if ( ! $riaz_email && ! $riaz_phone ) : ?>
						<li><a href="<?php echo esc_url( riaz_link_url( '#contact' ) ); ?>"><?php esc_html_e( 'Get in touch', 'riaz-portfolio' ); ?></a></li>
					<?php endif; ?>
				</ul>
			</div>
		</div>

		<div class="footer-bottom">
			<small><?php echo esc_html( riaz_copyright() ); ?></small>
			<div class="footer-bottom-links">
				<?php
				if ( has_nav_menu( 'footer_bottom' ) ) {
					wp_nav_menu(
						array(
							'theme_location'       => 'footer_bottom',
							'container'            => 'nav',
							'container_aria_label' => __( 'Legal', 'riaz-portfolio' ),
							'depth'                => 1,
						)
					);
				} elseif ( function_exists( 'the_privacy_policy_link' ) ) {
					the_privacy_policy_link();
				}
				?>
				<?php if ( riaz_mod_bool( 'footer_back_to_top' ) ) : ?>
					<a href="#top" data-back-to-top><?php esc_html_e( 'Back to top', 'riaz-portfolio' ); ?> <span aria-hidden="true">↑</span></a>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<?php if ( riaz_mod_bool( 'footer_wordmark' ) ) : ?>
		<p class="footer-wordmark" aria-hidden="true"><?php echo esc_html( $riaz_mark ? $riaz_mark : $riaz_brand ); ?></p>
	<?php endif; ?>
</footer>
