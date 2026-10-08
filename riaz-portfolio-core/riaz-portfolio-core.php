<?php
/**
 * Plugin Name:       Riaz Portfolio Core
 * Description:       Companion plugin for the Riaz Portfolio theme: services, portfolio, case studies, testimonials, team, FAQ and pricing content types, the Theme Section block, Elementor widget, shortcodes, contact form, SEO meta and schema, and the starter content importer. Your content stays safe if you ever switch themes.
 * Version:           1.0.0
 * Requires at least: 6.6
 * Requires PHP:      7.4
 * Author:            Riaz Ahmed
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       riaz-portfolio-core
 * Domain Path:       /languages
 *
 * @package Riaz_Portfolio_Core
 */

defined( 'ABSPATH' ) || exit;

define( 'RIAZ_CORE_VERSION', '1.0.0' );
define( 'RIAZ_CORE_FILE', __FILE__ );
define( 'RIAZ_CORE_DIR', plugin_dir_path( __FILE__ ) );
define( 'RIAZ_CORE_URL', plugin_dir_url( __FILE__ ) );

foreach ( array( 'helpers', 'post-types', 'meta-boxes', 'settings', 'seo', 'performance', 'contact-form', 'shortcodes', 'blocks', 'elementor', 'divi', 'importer' ) as $riaz_core_file ) {
	require_once RIAZ_CORE_DIR . 'includes/' . $riaz_core_file . '.php';
}
unset( $riaz_core_file );

/**
 * Load translations.
 */
function riaz_core_load_textdomain() {
	load_plugin_textdomain( 'riaz-portfolio-core', false, dirname( plugin_basename( RIAZ_CORE_FILE ) ) . '/languages' );
}
add_action( 'init', 'riaz_core_load_textdomain', 0 );

/**
 * Register content types and refresh permalinks on activation.
 */
function riaz_core_activate() {
	riaz_core_register_post_types();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'riaz_core_activate' );

/**
 * Refresh permalinks on deactivation.
 */
function riaz_core_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'riaz_core_deactivate' );
