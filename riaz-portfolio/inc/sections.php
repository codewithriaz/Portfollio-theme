<?php
/**
 * Homepage section registry and renderer.
 *
 * Every section is a template part in template-parts/sections/ and can be
 * rendered on the homepage, through the [riaz_section] shortcode, the
 * "Theme Section" block, or the Elementor widget.
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registered sections: slug => label.
 *
 * @return array<string,string>
 */
function riaz_get_sections() {
	$sections = array(
		'hero'         => __( 'Hero', 'riaz-portfolio' ),
		'marquee'      => __( 'Who I help (marquee)', 'riaz-portfolio' ),
		'about'        => __( 'About', 'riaz-portfolio' ),
		'stats'        => __( 'Statistics', 'riaz-portfolio' ),
		'services'     => __( 'Services', 'riaz-portfolio' ),
		'approach'     => __( 'Approach / process', 'riaz-portfolio' ),
		'portfolio'    => __( 'Portfolio', 'riaz-portfolio' ),
		'case_studies' => __( 'Case studies', 'riaz-portfolio' ),
		'testimonials' => __( 'Testimonials', 'riaz-portfolio' ),
		'team'         => __( 'Team', 'riaz-portfolio' ),
		'pricing'      => __( 'Pricing', 'riaz-portfolio' ),
		'faq'          => __( 'FAQ', 'riaz-portfolio' ),
		'cta'          => __( 'Call to action', 'riaz-portfolio' ),
		'contact'      => __( 'Contact', 'riaz-portfolio' ),
	);

	/**
	 * Filter the available sections. Add a slug here and a matching
	 * template-parts/sections/{slug}.php file in a child theme.
	 *
	 * @param array $sections Slug => label.
	 */
	return apply_filters( 'riaz_sections', $sections );
}

/**
 * Default order and visibility (matches the original one-page design).
 *
 * @return array[] List of { id, on }.
 */
function riaz_default_section_order() {
	$off   = array( 'contact' );
	$order = array();
	foreach ( array_keys( riaz_get_sections() ) as $slug ) {
		$order[] = array(
			'id' => $slug,
			'on' => ! in_array( $slug, $off, true ),
		);
	}
	return $order;
}

/**
 * Sections enabled on the homepage, in order. New sections added by a theme
 * update are appended so saved settings never hide them unexpectedly.
 *
 * @return string[]
 */
function riaz_get_enabled_sections() {
	$saved = json_decode( (string) riaz_mod( 'home_sections' ), true );
	$all   = riaz_get_sections();
	if ( ! is_array( $saved ) ) {
		$saved = riaz_default_section_order();
	}

	$seen    = array();
	$enabled = array();
	foreach ( $saved as $row ) {
		if ( ! is_array( $row ) || empty( $row['id'] ) || ! isset( $all[ $row['id'] ] ) || isset( $seen[ $row['id'] ] ) ) {
			continue;
		}
		$seen[ $row['id'] ] = true;
		if ( ! empty( $row['on'] ) ) {
			$enabled[] = $row['id'];
		}
	}
	foreach ( riaz_default_section_order() as $row ) {
		if ( ! isset( $seen[ $row['id'] ] ) && $row['on'] ) {
			$enabled[] = $row['id'];
		}
	}
	return $enabled;
}

/**
 * Choices for block, widget and shortcode UIs.
 *
 * @return array<string,string>
 */
function riaz_get_section_choices() {
	return riaz_get_sections();
}

/**
 * HTML id for a section.
 *
 * @param string $slug Section slug.
 * @return string
 */
function riaz_section_anchor( $slug ) {
	$anchors = array(
		'hero'         => 'top',
		'cta'          => 'contact',
		'contact'      => 'get-in-touch',
		'case_studies' => 'case-studies',
	);
	return isset( $anchors[ $slug ] ) ? $anchors[ $slug ] : str_replace( '_', '-', $slug );
}

/**
 * Render a section.
 *
 * @param string $slug Section slug.
 * @param array  $args Optional: 'id' (anchor override), 'context' ('home', 'block', 'shortcode', 'single').
 */
function riaz_render_section( $slug, $args = array() ) {
	if ( ! array_key_exists( $slug, riaz_get_sections() ) ) {
		return;
	}
	$args         = wp_parse_args(
		$args,
		array(
			'id'      => riaz_section_anchor( $slug ),
			'context' => 'home',
		)
	);
	$args['slug'] = $slug;

	/**
	 * Fires before a section is rendered.
	 *
	 * @param string $slug Section slug.
	 * @param array  $args Arguments.
	 */
	do_action( 'riaz_before_section', $slug, $args );

	get_template_part( 'template-parts/sections/' . str_replace( '_', '-', $slug ), null, $args );

	/**
	 * Fires after a section is rendered.
	 *
	 * @param string $slug Section slug.
	 * @param array  $args Arguments.
	 */
	do_action( 'riaz_after_section', $slug, $args );
}

/**
 * Render a section to a string.
 *
 * @param string $slug Section slug.
 * @param array  $args See riaz_render_section().
 * @return string
 */
function riaz_get_section_html( $slug, $args = array() ) {
	ob_start();
	riaz_render_section( $slug, $args );
	return (string) ob_get_clean();
}

/**
 * Render all enabled homepage sections in order.
 */
function riaz_render_homepage_sections() {
	foreach ( riaz_get_enabled_sections() as $slug ) {
		riaz_render_section( $slug );
	}
}

/**
 * Query published items of a theme post type for a section.
 *
 * @param string $post_type Post type.
 * @param int    $count     Maximum items.
 * @param array  $extra     Extra WP_Query arguments.
 * @return WP_Query
 */
function riaz_section_query( $post_type, $count, $extra = array() ) {
	if ( ! post_type_exists( $post_type ) ) {
		return new WP_Query();
	}
	return new WP_Query(
		array_merge(
			array(
				'post_type'           => $post_type,
				'post_status'         => 'publish',
				'posts_per_page'      => max( 1, absint( $count ) ),
				'orderby'             => array(
					'menu_order' => 'ASC',
					'date'       => 'DESC',
				),
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
			),
			$extra
		)
	);
}

/**
 * Whether a post type has at least one published item (cached per request).
 *
 * @param string $post_type Post type.
 * @return bool
 */
function riaz_has_items( $post_type ) {
	static $cache = array();
	if ( ! post_type_exists( $post_type ) ) {
		return false;
	}
	if ( ! isset( $cache[ $post_type ] ) ) {
		$counts              = wp_count_posts( $post_type );
		$cache[ $post_type ] = isset( $counts->publish ) && $counts->publish > 0;
	}
	return $cache[ $post_type ];
}

/**
 * Whether a section will produce visible output (used for menus and anchors).
 *
 * @param string $slug Section slug.
 * @return bool
 */
function riaz_section_has_content( $slug ) {
	$post_types = array(
		'services'     => 'riaz_service',
		'portfolio'    => 'riaz_portfolio',
		'case_studies' => 'riaz_case_study',
		'testimonials' => 'riaz_testimonial',
		'team'         => 'riaz_team',
		'pricing'      => 'riaz_pricing',
		'faq'          => 'riaz_faq',
	);
	if ( isset( $post_types[ $slug ] ) ) {
		return riaz_has_items( $post_types[ $slug ] );
	}
	if ( 'stats' === $slug ) {
		return (bool) riaz_mod_list( 'stats_items' );
	}
	if ( 'marquee' === $slug ) {
		return (bool) riaz_mod_list( 'marquee_items' );
	}
	if ( 'approach' === $slug ) {
		return (bool) riaz_mod_list( 'approach_steps' );
	}
	return true;
}

/**
 * Data attributes that mark a section for Customizer selective refresh.
 *
 * @param string $slug Section slug.
 */
function riaz_section_attrs( $slug ) {
	printf( ' data-riaz-section="%s"', esc_attr( $slug ) );
}
