<?php
/**
 * Masthead: logo, primary navigation, call-to-action and mobile menu.
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;

$riaz_classes   = array( 'site-header' );
$riaz_cta_label = riaz_mod( 'header_cta_label' );
$riaz_cta_url   = riaz_link_url( riaz_mod( 'header_cta_url' ) );
$riaz_show_cta  = riaz_mod_bool( 'header_cta_show' ) && $riaz_cta_label && $riaz_cta_url;

if ( ! riaz_header_is_overlay() ) {
	$riaz_classes[] = 'is-solid';
}
?>
<header id="masthead" class="<?php echo esc_attr( implode( ' ', $riaz_classes ) ); ?>" data-header>
	<div class="site-header-bar">
		<?php get_template_part( 'template-parts/header/branding' ); ?>

		<nav id="site-navigation" class="main-nav" aria-label="<?php esc_attr_e( 'Primary', 'riaz-portfolio' ); ?>" data-nav>
			<span class="nav-pill" aria-hidden="true"></span>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'menu_id'        => 'primary-menu',
					'menu_class'     => 'menu',
					'container'      => false,
					'depth'          => 3,
					'fallback_cb'    => 'riaz_primary_menu_fallback',
				)
			);
			?>
		</nav>

		<?php if ( $riaz_show_cta ) : ?>
			<a class="header-cta" href="<?php echo esc_url( $riaz_cta_url ); ?>">
				<?php echo esc_html( $riaz_cta_label ); ?>
				<?php riaz_icon( 'arrow-up-right', 15 ); ?>
			</a>
		<?php endif; ?>

		<button type="button" class="menu-toggle" aria-expanded="false" aria-controls="mobile-menu" data-menu-toggle data-label-open="<?php esc_attr_e( 'Open menu', 'riaz-portfolio' ); ?>" data-label-close="<?php esc_attr_e( 'Close menu', 'riaz-portfolio' ); ?>">
			<span class="screen-reader-text"><?php esc_html_e( 'Open menu', 'riaz-portfolio' ); ?></span>
			<?php riaz_icon( 'menu', 20, array( 'class' => 'icon-open' ) ); ?>
			<?php riaz_icon( 'x', 20, array( 'class' => 'icon-close' ) ); ?>
		</button>
	</div>

	<nav id="mobile-menu" class="mobile-menu" aria-label="<?php esc_attr_e( 'Mobile', 'riaz-portfolio' ); ?>" hidden data-mobile-menu>
		<?php
		// The same menu again for small screens; drop item IDs to keep them unique.
		add_filter( 'nav_menu_item_id', '__return_empty_string' );
		wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'menu_id'        => 'mobile-menu-list',
				'menu_class'     => 'menu',
				'container'      => false,
				'depth'          => 3,
				'fallback_cb'    => 'riaz_primary_menu_fallback',
			)
		);
		remove_filter( 'nav_menu_item_id', '__return_empty_string' );
		?>
		<?php if ( $riaz_show_cta ) : ?>
			<a class="mobile-menu-cta" href="<?php echo esc_url( $riaz_cta_url ); ?>">
				<?php echo esc_html( $riaz_cta_label ); ?>
				<?php riaz_icon( 'arrow-up-right', 15 ); ?>
			</a>
		<?php endif; ?>
	</nav>
</header>
