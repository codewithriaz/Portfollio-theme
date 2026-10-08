<?php
/**
 * Riaz Portfolio functions and definitions.
 *
 * Each concern lives in its own file under /inc so the theme stays easy to
 * navigate and child themes can override individual template parts.
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;

define( 'RIAZ_VERSION', '1.0.0' );
define( 'RIAZ_DIR', get_template_directory() );
define( 'RIAZ_URI', get_template_directory_uri() );

$riaz_includes = array(
	'inc/helpers.php',
	'inc/icons.php',
	'inc/sections.php',
	'inc/customizer/schema.php',
	'inc/setup.php',
	'inc/enqueue.php',
	'inc/template-tags.php',
	'inc/meta-boxes.php',
	'inc/customizer/customizer.php',
	'inc/compat/gutenberg.php',
	'inc/compat/elementor.php',
	'inc/compat/divi.php',
	'inc/companion.php',
	'inc/admin/setup-page.php',
);

foreach ( $riaz_includes as $riaz_file ) {
	require_once RIAZ_DIR . '/' . $riaz_file;
}
unset( $riaz_includes, $riaz_file );
