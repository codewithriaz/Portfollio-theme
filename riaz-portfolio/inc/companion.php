<?php
/**
 * Companion plugin integration.
 *
 * Content types, shortcodes, the Theme Section block, the Elementor widget,
 * the contact form, SEO schema and the importer live in the bundled
 * "Riaz Portfolio Core" plugin, so content is never lost when switching
 * themes. This file offers a one-click install of the bundled copy and
 * passes theme data (social links, portrait, contact email) to it.
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * Plugin file relative to the plugins folder.
 */
const RIAZ_COMPANION = 'riaz-portfolio-core/riaz-portfolio-core.php';

/**
 * Companion status: 'active', 'installed' or 'missing'.
 *
 * @return string
 */
function riaz_companion_status() {
	if ( defined( 'RIAZ_CORE_VERSION' ) ) {
		return 'active';
	}
	return file_exists( WP_PLUGIN_DIR . '/' . RIAZ_COMPANION ) ? 'installed' : 'missing';
}

/**
 * Explanation and install/activate button.
 */
function riaz_companion_notice_body() {
	$status = riaz_companion_status();
	if ( 'active' === $status ) {
		return;
	}
	$can = 'missing' === $status ? current_user_can( 'install_plugins' ) : current_user_can( 'activate_plugins' );
	?>
	<p><?php echo wp_kses_post( __( 'Install the bundled <strong>Riaz Portfolio Core</strong> plugin to add services, portfolio, case studies, testimonials, team, FAQs, pricing, the contact form, SEO schema and one-click starter content. Keeping these in a plugin means your content stays safe if you ever change theme.', 'riaz-portfolio' ) ); ?></p>
	<?php if ( $can ) : ?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="riaz_install_companion">
			<?php wp_nonce_field( 'riaz_install_companion', 'riaz_companion_nonce' ); ?>
			<button type="submit" class="button button-primary">
				<?php 'missing' === $status ? esc_html_e( 'Install & activate Riaz Portfolio Core', 'riaz-portfolio' ) : esc_html_e( 'Activate Riaz Portfolio Core', 'riaz-portfolio' ); ?>
			</button>
		</form>
	<?php else : ?>
		<p><em><?php esc_html_e( 'Ask a site administrator to install it.', 'riaz-portfolio' ); ?></em></p>
	<?php endif; ?>
	<?php
}

/**
 * Admin notice while the companion plugin is not active.
 */
function riaz_companion_admin_notice() {
	if ( 'active' === riaz_companion_status() || ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( $screen && in_array( $screen->id, array( 'appearance_page_riaz-setup', 'update' ), true ) ) {
		return;
	}
	echo '<div class="notice notice-warning"><p><strong>' . esc_html__( 'Riaz Portfolio', 'riaz-portfolio' ) . '</strong></p>';
	riaz_companion_notice_body();
	echo '<p></p></div>';
}
add_action( 'admin_notices', 'riaz_companion_admin_notice' );

/**
 * Install (from the copy bundled with the theme) and activate the plugin.
 */
function riaz_install_companion() {
	check_admin_referer( 'riaz_install_companion', 'riaz_companion_nonce' );

	$status = riaz_companion_status();
	if ( 'missing' === $status ) {
		if ( ! current_user_can( 'install_plugins' ) ) {
			wp_die( esc_html__( 'You are not allowed to install plugins.', 'riaz-portfolio' ), 403 );
		}
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		$package  = get_theme_file_path( 'plugins/riaz-portfolio-core.zip' );
		$upgrader = new Plugin_Upgrader( new Automatic_Upgrader_Skin() );
		$result   = file_exists( $package ) ? $upgrader->install( $package ) : false;
		if ( true !== $result ) {
			wp_die( esc_html__( 'The plugin could not be installed automatically. Upload plugins/riaz-portfolio-core.zip from the theme folder in Plugins → Add New → Upload Plugin.', 'riaz-portfolio' ) );
		}
	}

	if ( ! current_user_can( 'activate_plugins' ) ) {
		wp_die( esc_html__( 'You are not allowed to activate plugins.', 'riaz-portfolio' ), 403 );
	}
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
	$activated = activate_plugin( RIAZ_COMPANION );
	if ( is_wp_error( $activated ) ) {
		wp_die( esc_html( $activated->get_error_message() ) );
	}

	wp_safe_redirect( admin_url( 'themes.php?page=riaz-setup' ) );
	exit;
}
add_action( 'admin_post_riaz_install_companion', 'riaz_install_companion' );

/* ---------- Theme data for the plugin ---------- */

/**
 * Social profile URLs for the schema "sameAs" list.
 *
 * @param string[] $urls URLs.
 * @return string[]
 */
function riaz_companion_same_as( $urls ) {
	foreach ( riaz_get_social_links() as $network => $link ) {
		if ( 'whatsapp' !== $network ) {
			$urls[] = $link['url'];
		}
	}
	return $urls;
}
add_filter( 'riaz_core_same_as', 'riaz_companion_same_as' );

/**
 * The hero portrait represents the person in schema.
 *
 * @return string
 */
function riaz_companion_entity_image() {
	$id = absint( riaz_mod( 'hero_image' ) );
	return $id ? (string) wp_get_attachment_image_url( $id, 'full' ) : riaz_asset( 'images/hero-portrait.webp' );
}
add_filter( 'riaz_core_entity_image', 'riaz_companion_entity_image' );

/**
 * Bundled 1200×630 share image.
 *
 * @return array
 */
function riaz_companion_share_image() {
	return array( riaz_asset( 'images/share-default.jpg' ), 1200, 630 );
}
add_filter( 'riaz_core_default_share_image', 'riaz_companion_share_image' );

/**
 * Output FAQPage schema only where the FAQ section is shown.
 *
 * @return bool
 */
function riaz_companion_faq_schema() {
	return riaz_is_sections_page() && in_array( 'faq', riaz_get_enabled_sections(), true );
}
add_filter( 'riaz_core_faq_schema', 'riaz_companion_faq_schema' );

/**
 * Contact form recipient from Theme Options → Contact Details.
 *
 * @param string $to Default recipient.
 * @return string
 */
function riaz_companion_recipient( $to ) {
	$custom = riaz_mod( 'contact_form_to' );
	$custom = $custom ? $custom : riaz_mod( 'contact_email' );
	return $custom ? $custom : $to;
}
add_filter( 'riaz_core_contact_recipient', 'riaz_companion_recipient' );
