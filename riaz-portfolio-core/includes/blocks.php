<?php
/**
 * Block editor: the "Theme Section" block, its inserter category and
 * ready-made page patterns built from it.
 *
 * @package Riaz_Portfolio_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the block and patterns.
 */
function riaz_core_register_blocks() {
	register_block_type( RIAZ_CORE_DIR . 'blocks/section' );

	$handle   = generate_block_asset_handle( 'riaz/section', 'editorScript' );
	$sections = function_exists( 'riaz_get_section_choices' ) ? riaz_get_section_choices() : array();
	wp_add_inline_script( $handle, 'window.riazSections = ' . wp_json_encode( $sections ) . ';', 'before' );
	wp_set_script_translations( $handle, 'riaz-portfolio-core', RIAZ_CORE_DIR . 'languages' );

	if ( ! riaz_core_theme_active() ) {
		return;
	}

	if ( ! WP_Block_Pattern_Categories_Registry::get_instance()->is_registered( 'riaz-portfolio' ) ) {
		register_block_pattern_category( 'riaz-portfolio', array( 'label' => __( 'Riaz Portfolio', 'riaz-portfolio-core' ) ) );
	}

	$patterns = array(
		'homepage'         => array(
			__( 'Full homepage (all sections)', 'riaz-portfolio-core' ),
			__( 'Every theme section in the default order. Reorder, remove or add blocks between them.', 'riaz-portfolio-core' ),
			array( 'hero', 'marquee', 'about', 'stats', 'services', 'approach', 'portfolio', 'case_studies', 'testimonials', 'faq', 'cta' ),
		),
		'landing-services' => array(
			__( 'Services landing page', 'riaz-portfolio-core' ),
			__( 'Services, process, pricing, FAQ and a call to action.', 'riaz-portfolio-core' ),
			array( 'services', 'approach', 'pricing', 'faq', 'cta' ),
		),
		'about-page'       => array(
			__( 'About page', 'riaz-portfolio-core' ),
			__( 'About, statistics, team, testimonials and a call to action.', 'riaz-portfolio-core' ),
			array( 'about', 'stats', 'team', 'testimonials', 'cta' ),
		),
		'contact-page'     => array(
			__( 'Contact page', 'riaz-portfolio-core' ),
			__( 'Contact details with the form, followed by the FAQ.', 'riaz-portfolio-core' ),
			array( 'contact', 'faq' ),
		),
	);

	foreach ( $patterns as $slug => $pattern ) {
		$content = '';
		foreach ( $pattern[2] as $section ) {
			$content .= '<!-- wp:riaz/section {"section":"' . $section . '","align":"full"} /-->' . "\n";
		}
		register_block_pattern(
			'riaz-portfolio/' . $slug,
			array(
				'title'         => $pattern[0],
				'description'   => $pattern[1],
				'categories'    => array( 'riaz-portfolio' ),
				'content'       => $content,
				'viewportWidth' => 1400,
			)
		);
	}
}
add_action( 'init', 'riaz_core_register_blocks' );

/**
 * Inserter category for the block.
 *
 * @param array $categories Categories.
 * @return array
 */
function riaz_core_block_categories( $categories ) {
	foreach ( $categories as $category ) {
		if ( 'riaz-portfolio' === $category['slug'] ) {
			return $categories;
		}
	}
	array_unshift(
		$categories,
		array(
			'slug'  => 'riaz-portfolio',
			'title' => __( 'Riaz Portfolio', 'riaz-portfolio-core' ),
			'icon'  => null,
		)
	);
	return $categories;
}
add_filter( 'block_categories_all', 'riaz_core_block_categories' );
