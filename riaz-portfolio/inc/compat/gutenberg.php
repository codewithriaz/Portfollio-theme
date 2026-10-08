<?php
/**
 * Block editor integration: block styles, the pattern category, editor
 * styles and a color palette that follows the Customizer brand colors.
 * (The "Theme Section" block itself ships with the Riaz Portfolio Core plugin.)
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * Pattern category and block style variations.
 */
function riaz_register_block_extras() {
	if ( ! WP_Block_Pattern_Categories_Registry::get_instance()->is_registered( 'riaz-portfolio' ) ) {
		register_block_pattern_category(
			'riaz-portfolio',
			array( 'label' => __( 'Riaz Portfolio', 'riaz-portfolio' ) )
		);
	}

	$styles = array(
		'core/group'   => array( 'riaz-card', __( 'Card', 'riaz-portfolio' ) ),
		'core/heading' => array( 'riaz-gradient', __( 'Gradient', 'riaz-portfolio' ) ),
		'core/image'   => array( 'riaz-elevated', __( 'Rounded & elevated', 'riaz-portfolio' ) ),
		'core/list'    => array( 'riaz-checks', __( 'Check marks', 'riaz-portfolio' ) ),
	);
	foreach ( $styles as $block => $style ) {
		register_block_style(
			$block,
			array(
				'name'  => $style[0],
				'label' => $style[1],
			)
		);
	}
}
add_action( 'init', 'riaz_register_block_extras' );

/**
 * Google font (if selected) inside the editor canvas.
 */
function riaz_editor_fonts() {
	$url = riaz_google_fonts_url();
	if ( $url ) {
		add_editor_style( $url );
	}
}
add_action( 'init', 'riaz_editor_fonts' );

/**
 * Pass the Customizer CSS variables to the editor canvas.
 *
 * @param array $settings Editor settings.
 * @return array
 */
function riaz_editor_dynamic_css( $settings ) {
	$css = riaz_dynamic_css();
	if ( $css ) {
		$settings['styles'][] = array( 'css' => $css );
	}
	return $settings;
}
add_filter( 'block_editor_settings_all', 'riaz_editor_dynamic_css' );

/**
 * Keep the editor color palette in sync with the Customizer brand colors.
 *
 * @param WP_Theme_JSON_Data $theme_json Theme JSON data.
 * @return WP_Theme_JSON_Data
 */
function riaz_sync_palette( $theme_json ) {
	if ( ! did_action( 'init' ) ) {
		return $theme_json;
	}
	$map     = array(
		'primary'      => array( 'color_primary', __( 'Primary', 'riaz-portfolio' ) ),
		'accent'       => array( 'color_accent', __( 'Accent', 'riaz-portfolio' ) ),
		'accent-light' => array( 'color_light', __( 'Light accent', 'riaz-portfolio' ) ),
		'dark'         => array( 'color_dark', __( 'Dark', 'riaz-portfolio' ) ),
		'darker'       => array( 'color_darker', __( 'Darkest', 'riaz-portfolio' ) ),
		'ink'          => array( 'color_text', __( 'Text', 'riaz-portfolio' ) ),
		'ink-soft'     => array( 'color_text_muted', __( 'Muted text', 'riaz-portfolio' ) ),
		'surface'      => array( 'color_surface', __( 'Surface', 'riaz-portfolio' ) ),
	);
	$palette = array();
	foreach ( $map as $slug => $def ) {
		$palette[] = array(
			'slug'  => $slug,
			'name'  => $def[1],
			'color' => sanitize_hex_color( riaz_mod( $def[0] ) ),
		);
	}
	$palette[] = array(
		'slug'  => 'white',
		'name'  => __( 'White', 'riaz-portfolio' ),
		'color' => '#ffffff',
	);

	return $theme_json->update_with(
		array(
			'version'  => 3,
			'settings' => array(
				'color' => array( 'palette' => $palette ),
			),
		)
	);
}
add_filter( 'wp_theme_json_data_theme', 'riaz_sync_palette' );
