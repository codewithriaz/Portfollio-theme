<?php
/**
 * Shortcodes for page builders (Divi, Elementor, WPBakery) and the classic editor.
 * Section, social, button and icon shortcodes need the Riaz Portfolio theme.
 *
 *   [riaz_section name="services"]   Any homepage section (see riaz_get_sections()).
 *   [riaz_contact_form]              The built-in contact form.
 *   [riaz_social style="light"]      Social profile icons.
 *   [riaz_button label="" url="" style="primary|light|ghost|outline"]
 *   [riaz_icon name="search" size="20"]
 *
 * @package Riaz_Portfolio_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * [riaz_section name="..." id="..."]
 *
 * @param array $atts Attributes.
 * @return string
 */
function riaz_core_section_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'name' => '',
			'id'   => '',
		),
		$atts,
		'riaz_section'
	);
	if ( ! riaz_core_theme_active() ) {
		return '';
	}
	$slug = sanitize_key( str_replace( '-', '_', $atts['name'] ) );
	if ( ! array_key_exists( $slug, riaz_get_sections() ) ) {
		return '';
	}
	$args = array( 'context' => 'shortcode' );
	if ( $atts['id'] ) {
		$args['id'] = sanitize_html_class( $atts['id'] );
	}
	return riaz_get_section_html( $slug, $args );
}
add_shortcode( 'riaz_section', 'riaz_core_section_shortcode' );

/**
 * [riaz_contact_form]
 *
 * @return string
 */
function riaz_core_contact_form_shortcode() {
	$args = array();
	if ( function_exists( 'riaz_mod' ) ) {
		$args['note'] = riaz_mod( 'contact_form_note' );
	}
	ob_start();
	riaz_core_contact_form( $args );
	return (string) ob_get_clean();
}
add_shortcode( 'riaz_contact_form', 'riaz_core_contact_form_shortcode' );

/**
 * [riaz_social style="light"]
 *
 * @param array $atts Attributes.
 * @return string
 */
function riaz_core_social_shortcode( $atts ) {
	$atts = shortcode_atts( array( 'style' => '' ), $atts, 'riaz_social' );
	if ( ! function_exists( 'riaz_social_list' ) ) {
		return '';
	}
	ob_start();
	riaz_social_list( 'light' === $atts['style'] ? 'is-light' : '' );
	return (string) ob_get_clean();
}
add_shortcode( 'riaz_social', 'riaz_core_social_shortcode' );

/**
 * [riaz_button label="..." url="..." style="primary"]
 *
 * @param array $atts Attributes.
 * @return string
 */
function riaz_core_button_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'label' => '',
			'url'   => '',
			'style' => 'primary',
		),
		$atts,
		'riaz_button'
	);
	if ( ! function_exists( 'riaz_button' ) ) {
		return '';
	}
	ob_start();
	riaz_button( sanitize_text_field( $atts['label'] ), esc_url_raw( $atts['url'] ), sanitize_key( $atts['style'] ) );
	return (string) ob_get_clean();
}
add_shortcode( 'riaz_button', 'riaz_core_button_shortcode' );

/**
 * [riaz_icon name="search" size="20"]
 *
 * @param array $atts Attributes.
 * @return string
 */
function riaz_core_icon_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'name' => 'sparkles',
			'size' => 20,
		),
		$atts,
		'riaz_icon'
	);
	if ( ! function_exists( 'riaz_get_icon' ) ) {
		return '';
	}
	return riaz_get_icon( sanitize_key( $atts['name'] ), min( 96, absint( $atts['size'] ) ) );
}
add_shortcode( 'riaz_icon', 'riaz_core_icon_shortcode' );
