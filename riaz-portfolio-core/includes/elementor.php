<?php
/**
 * Elementor: the "Theme Section" widget.
 *
 * @package Riaz_Portfolio_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Widget panel category.
 *
 * @param \Elementor\Elements_Manager $elements_manager Elements manager.
 */
function riaz_core_elementor_category( $elements_manager ) {
	$elements_manager->add_category(
		'riaz-portfolio',
		array(
			'title' => esc_html__( 'Riaz Portfolio', 'riaz-portfolio-core' ),
			'icon'  => 'eicon-site-identity',
		)
	);
}
add_action( 'elementor/elements/categories_registered', 'riaz_core_elementor_category' );

/**
 * Register the widget (only with the Riaz Portfolio theme, which renders the sections).
 *
 * @param \Elementor\Widgets_Manager $widgets_manager Widgets manager.
 */
function riaz_core_elementor_widgets( $widgets_manager ) {
	if ( ! riaz_core_theme_active() ) {
		return;
	}
	require_once RIAZ_CORE_DIR . 'includes/class-riaz-core-elementor-section-widget.php';
	$widgets_manager->register( new Riaz_Core_Elementor_Section_Widget() );
}
add_action( 'elementor/widgets/register', 'riaz_core_elementor_widgets' );
