<?php
/**
 * Elementor integration: Theme Builder locations (Pro) and header style.
 * The "Theme Section" widget ships with the Riaz Portfolio Core plugin.
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * Let Elementor Pro replace the header, footer, single and archive templates.
 *
 * @param \ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $manager Locations manager.
 */
function riaz_elementor_locations( $manager ) {
	$manager->register_all_core_location();
}
add_action( 'elementor/theme/register_locations', 'riaz_elementor_locations' );

/**
 * Elementor's "Full Width" and "Canvas" templates start without a dark hero,
 * so use the solid header there.
 *
 * @param bool $overlay Overlay state.
 * @return bool
 */
function riaz_elementor_header_style( $overlay ) {
	if ( is_singular() && in_array( get_page_template_slug( get_queried_object_id() ), array( 'elementor_header_footer', 'elementor_canvas' ), true ) ) {
		return 'overlay' === riaz_meta( get_queried_object_id(), 'header_style' );
	}
	return $overlay;
}
add_filter( 'riaz_header_is_overlay', 'riaz_elementor_header_style' );
