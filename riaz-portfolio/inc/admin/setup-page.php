<?php
/**
 * Appearance → Riaz Portfolio: getting started, shortcuts, starter content
 * import and compatibility status. Also shows a one-time welcome notice.
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the admin page.
 */
function riaz_admin_menu() {
	add_theme_page(
		__( 'Riaz Portfolio Setup', 'riaz-portfolio' ),
		__( 'Riaz Portfolio', 'riaz-portfolio' ),
		'edit_theme_options',
		'riaz-setup',
		'riaz_render_setup_page'
	);
}
add_action( 'admin_menu', 'riaz_admin_menu' );

/**
 * Admin page styles.
 *
 * @param string $hook Page hook.
 */
function riaz_admin_assets( $hook ) {
	if ( 'appearance_page_riaz-setup' === $hook ) {
		wp_enqueue_style( 'riaz-admin', riaz_asset( 'css/admin.css' ), array(), riaz_asset_version( 'assets/css/admin.css' ) );
	}
	if ( in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		wp_add_inline_style( 'wp-admin', '.riaz-meta-field{margin:0 0 14px}.riaz-meta-field:last-child{margin-bottom:0}' );
	}
}
add_action( 'admin_enqueue_scripts', 'riaz_admin_assets' );

/**
 * Show the welcome notice after activation.
 */
function riaz_after_switch_theme() {
	update_option( 'riaz_show_welcome', 1, false );
}
add_action( 'after_switch_theme', 'riaz_after_switch_theme' );

/**
 * One-time welcome notice.
 */
function riaz_welcome_notice() {
	if ( ! get_option( 'riaz_show_welcome' ) || ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( $screen && 'appearance_page_riaz-setup' === $screen->id ) {
		return;
	}
	$dismiss = wp_nonce_url( admin_url( 'admin-post.php?action=riaz_dismiss_welcome' ), 'riaz_dismiss_welcome' );
	?>
	<div class="notice notice-info">
		<p><strong><?php esc_html_e( 'Welcome to Riaz Portfolio!', 'riaz-portfolio' ); ?></strong> <?php esc_html_e( 'Import the starter services, FAQs and menus in one click, then edit everything in the Customizer.', 'riaz-portfolio' ); ?></p>
		<p>
			<a class="button button-primary" href="<?php echo esc_url( admin_url( 'themes.php?page=riaz-setup' ) ); ?>"><?php esc_html_e( 'Get started', 'riaz-portfolio' ); ?></a>
			<a class="button-link" style="margin-left:10px" href="<?php echo esc_url( $dismiss ); ?>"><?php esc_html_e( 'Dismiss', 'riaz-portfolio' ); ?></a>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'riaz_welcome_notice' );

/**
 * Dismiss the welcome notice.
 */
function riaz_dismiss_welcome() {
	check_admin_referer( 'riaz_dismiss_welcome' );
	if ( current_user_can( 'edit_theme_options' ) ) {
		delete_option( 'riaz_show_welcome' );
	}
	wp_safe_redirect( wp_get_referer() ? wp_get_referer() : admin_url() );
	exit;
}
add_action( 'admin_post_riaz_dismiss_welcome', 'riaz_dismiss_welcome' );

/**
 * Render the setup page.
 */
function riaz_render_setup_page() {
	delete_option( 'riaz_show_welcome' );

	$log      = get_transient( 'riaz_import_log_' . get_current_user_id() );
	$imported = get_option( 'riaz_starter_imported' );
	if ( $log ) {
		delete_transient( 'riaz_import_log_' . get_current_user_id() );
	}

	$core      = 'active' === riaz_companion_status();
	$customize = static function ( $type, $id ) {
		return admin_url( 'customize.php?autofocus[' . $type . ']=' . $id );
	};

	$shortcuts = array(
		array( 'admin-appearance', __( 'Homepage sections', 'riaz-portfolio' ), __( 'Edit, reorder and hide every section.', 'riaz-portfolio' ), $customize( 'panel', 'riaz_home' ) ),
		array( 'art', __( 'Brand colors & fonts', 'riaz-portfolio' ), __( 'Colors, typography and layout.', 'riaz-portfolio' ), $customize( 'panel', 'riaz_options' ) ),
		array( 'format-image', __( 'Logo & site title', 'riaz-portfolio' ), __( 'Upload your logo and favicon.', 'riaz-portfolio' ), $customize( 'section', 'title_tagline' ) ),
		array( 'menu', __( 'Menus', 'riaz-portfolio' ), __( 'Header and footer navigation.', 'riaz-portfolio' ), admin_url( 'nav-menus.php' ) ),
		array( 'lightbulb', __( 'Services', 'riaz-portfolio' ), __( 'Service cards with icons.', 'riaz-portfolio' ), admin_url( 'edit.php?post_type=riaz_service' ) ),
		array( 'portfolio', __( 'Portfolio', 'riaz-portfolio' ), __( 'Projects and categories.', 'riaz-portfolio' ), admin_url( 'edit.php?post_type=riaz_portfolio' ) ),
		array( 'chart-line', __( 'Case studies', 'riaz-portfolio' ), __( 'Stories with verified results.', 'riaz-portfolio' ), admin_url( 'edit.php?post_type=riaz_case_study' ) ),
		array( 'format-quote', __( 'Testimonials', 'riaz-portfolio' ), __( 'Written or YouTube video.', 'riaz-portfolio' ), admin_url( 'edit.php?post_type=riaz_testimonial' ) ),
		array( 'groups', __( 'Team', 'riaz-portfolio' ), __( 'People and social links.', 'riaz-portfolio' ), admin_url( 'edit.php?post_type=riaz_team' ) ),
		array( 'tag', __( 'Pricing', 'riaz-portfolio' ), __( 'Plans and features.', 'riaz-portfolio' ), admin_url( 'edit.php?post_type=riaz_pricing' ) ),
		array( 'editor-help', __( 'FAQs', 'riaz-portfolio' ), __( 'Questions and answers.', 'riaz-portfolio' ), admin_url( 'edit.php?post_type=riaz_faq' ) ),
		array( 'email', __( 'Contact & social', 'riaz-portfolio' ), __( 'Email, phone, form and profiles.', 'riaz-portfolio' ), $customize( 'section', 'riaz_contact' ) ),
	);
	if ( ! $core ) {
		$shortcuts = array_slice( $shortcuts, 0, 4 );
	}

	$seo    = function_exists( 'riaz_core_seo_plugin_active' ) && riaz_core_seo_plugin_active();
	$status = array(
		array( __( 'Riaz Portfolio Core', 'riaz-portfolio' ), $core, $core ? __( 'Content types, block, widget, shortcodes, contact form and schema are available.', 'riaz-portfolio' ) : __( 'Install it from the box above to unlock content types and the Theme Section block.', 'riaz-portfolio' ) ),
		array( __( 'Block editor (Gutenberg)', 'riaz-portfolio' ), true, __( 'Block styles, patterns and brand palette available.', 'riaz-portfolio' ) ),
		array( __( 'Elementor', 'riaz-portfolio' ), did_action( 'elementor/loaded' ) > 0, __( 'Theme Section widget and Theme Builder locations.', 'riaz-portfolio' ) ),
		array( __( 'Divi Builder', 'riaz-portfolio' ), defined( 'ET_BUILDER_PLUGIN_VERSION' ) || function_exists( 'et_setup_builder' ), __( 'Builder enabled on projects, case studies and services.', 'riaz-portfolio' ) ),
		array( __( 'SEO plugin', 'riaz-portfolio' ), $seo, $seo ? __( 'Detected — the built-in meta tags and schema are switched off.', 'riaz-portfolio' ) : __( 'None detected — Riaz Portfolio Core outputs meta tags, Open Graph and schema.', 'riaz-portfolio' ) ),
	);
	?>
	<div class="wrap riaz-setup">
		<div class="riaz-setup-hero">
			<div>
				<p class="riaz-setup-eyebrow"><?php echo esc_html( sprintf( /* translators: %s: version. */ __( 'Version %s', 'riaz-portfolio' ), RIAZ_VERSION ) ); ?></p>
				<h1><?php esc_html_e( 'Riaz Portfolio', 'riaz-portfolio' ); ?></h1>
				<p><?php esc_html_e( 'A fast, animated portfolio theme. Everything you see on the homepage is editable — no code required.', 'riaz-portfolio' ); ?></p>
			</div>
			<div class="riaz-setup-actions">
				<a class="button button-primary button-hero" href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>"><?php esc_html_e( 'Open the Customizer', 'riaz-portfolio' ); ?></a>
				<a class="button button-hero" href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'View site', 'riaz-portfolio' ); ?></a>
			</div>
		</div>

		<?php if ( $log ) : ?>
			<div class="notice notice-success"><p><strong><?php esc_html_e( 'Starter content imported.', 'riaz-portfolio' ); ?></strong></p><ul>
			<?php
			foreach ( (array) $log as $line ) :
				?>
				<li>✓ <?php echo esc_html( $line ); ?></li><?php endforeach; ?></ul></div>
		<?php endif; ?>

		<div class="riaz-setup-grid">
			<section class="riaz-card riaz-card-wide">
				<h2><?php esc_html_e( '1. Import starter content', 'riaz-portfolio' ); ?></h2>
				<p><?php esc_html_e( 'Recreates the demo: services, FAQs, pages and menus. Existing items with the same title are left untouched, so it is safe to run again.', 'riaz-portfolio' ); ?></p>
				<?php if ( $imported ) : ?>
					<p class="riaz-muted"><?php echo esc_html( sprintf( /* translators: %s: date. */ __( 'Last imported on %s.', 'riaz-portfolio' ), wp_date( get_option( 'date_format' ), (int) $imported ) ) ); ?></p>
				<?php endif; ?>
				<?php if ( function_exists( 'riaz_core_render_import_form' ) ) : ?>
					<?php riaz_core_render_import_form(); ?>
				<?php else : ?>
					<?php riaz_companion_notice_body(); ?>
				<?php endif; ?>
			</section>

			<section class="riaz-card riaz-card-wide">
				<h2><?php esc_html_e( '2. Make it yours', 'riaz-portfolio' ); ?></h2>
				<div class="riaz-shortcuts">
					<?php foreach ( $shortcuts as $shortcut ) : ?>
						<a class="riaz-shortcut" href="<?php echo esc_url( $shortcut[3] ); ?>">
							<span class="dashicons dashicons-<?php echo esc_attr( $shortcut[0] ); ?>" aria-hidden="true"></span>
							<strong><?php echo esc_html( $shortcut[1] ); ?></strong>
							<span><?php echo esc_html( $shortcut[2] ); ?></span>
						</a>
					<?php endforeach; ?>
				</div>
			</section>

			<section class="riaz-card">
				<h2><?php esc_html_e( 'Page builders & plugins', 'riaz-portfolio' ); ?></h2>
				<ul class="riaz-status">
					<?php foreach ( $status as $row ) : ?>
						<li>
							<span class="riaz-dot<?php echo $row[1] ? ' is-on' : ''; ?>" aria-hidden="true"></span>
							<div><strong><?php echo esc_html( $row[0] ); ?></strong> <em><?php echo $row[1] ? esc_html__( 'active', 'riaz-portfolio' ) : esc_html__( 'not active', 'riaz-portfolio' ); ?></em><br><span><?php echo esc_html( $row[2] ); ?></span></div>
						</li>
					<?php endforeach; ?>
				</ul>
			</section>

			<section class="riaz-card">
				<h2><?php esc_html_e( 'Use sections anywhere', 'riaz-portfolio' ); ?></h2>
				<p><?php esc_html_e( 'Every homepage section can be placed on any page:', 'riaz-portfolio' ); ?></p>
				<ul class="riaz-list">
					<li><strong><?php esc_html_e( 'Block editor:', 'riaz-portfolio' ); ?></strong> <?php esc_html_e( 'add the "Theme Section" block, or insert a pattern from the "Riaz Portfolio" category.', 'riaz-portfolio' ); ?></li>
					<li><strong><?php esc_html_e( 'Elementor:', 'riaz-portfolio' ); ?></strong> <?php esc_html_e( 'drag the "Theme Section" widget from the Riaz Portfolio category.', 'riaz-portfolio' ); ?></li>
					<li><strong><?php esc_html_e( 'Divi / classic editor:', 'riaz-portfolio' ); ?></strong> <code>[riaz_section name="services"]</code></li>
				</ul>
				<p class="riaz-muted"><?php echo esc_html( implode( ', ', array_keys( riaz_get_sections() ) ) ); ?></p>
			</section>
		</div>
	</div>
	<?php
}
