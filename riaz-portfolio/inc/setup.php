<?php
/**
 * Theme setup: supports, menus, widget areas, image sizes and body classes.
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register theme features.
 */
function riaz_setup() {
	load_theme_textdomain( 'riaz-portfolio', RIAZ_DIR . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'               => 200,
			'width'                => 600,
			'flex-height'          => true,
			'flex-width'           => true,
			'unlink-homepage-logo' => false,
		)
	);
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'link-color' );
	add_theme_support( 'custom-spacing' );
	add_theme_support( 'custom-units' );
	add_theme_support( 'custom-line-height' );

	add_editor_style( array( 'assets/css/main.css', 'assets/css/editor-style.css' ) );

	register_nav_menus(
		array(
			'primary'       => __( 'Primary menu', 'riaz-portfolio' ),
			'footer_col1'   => __( 'Footer — Column 1', 'riaz-portfolio' ),
			'footer_col2'   => __( 'Footer — Column 2', 'riaz-portfolio' ),
			'footer_bottom' => __( 'Footer — Bottom links', 'riaz-portfolio' ),
		)
	);

	add_image_size( 'riaz-card', 760, 570, true );
	add_image_size( 'riaz-portrait', 640, 800, true );
	add_image_size( 'riaz-wide', 1600, 900, true );
}
add_action( 'after_setup_theme', 'riaz_setup' );

/**
 * Content width for embeds and media.
 */
function riaz_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'riaz_content_width', 760 ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
}
add_action( 'after_setup_theme', 'riaz_content_width', 0 );

/**
 * Widget areas.
 */
function riaz_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'Blog Sidebar', 'riaz-portfolio' ),
			'id'            => 'sidebar-1',
			'description'   => __( 'Shown on blog pages when enabled in Theme Options → Blog.', 'riaz-portfolio' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'riaz_widgets_init' );

/**
 * Human-readable names for the new image sizes in the media modal.
 *
 * @param array $sizes Sizes.
 * @return array
 */
function riaz_image_size_names( $sizes ) {
	return array_merge(
		$sizes,
		array(
			'riaz-card'     => __( 'Card (4:3)', 'riaz-portfolio' ),
			'riaz-portrait' => __( 'Portrait (4:5)', 'riaz-portfolio' ),
			'riaz-wide'     => __( 'Wide (16:9)', 'riaz-portfolio' ),
		)
	);
}
add_filter( 'image_size_names_choose', 'riaz_image_size_names' );

/**
 * Whether the page starts with a dark area under the header (hero or banner),
 * so the transparent "glass" header is readable.
 *
 * @return bool
 */
function riaz_header_is_overlay() {
	$overlay = true;

	if ( is_singular() ) {
		$post_id = get_queried_object_id();
		$style   = riaz_meta( $post_id, 'header_style', 'auto' );
		if ( 'overlay' === $style ) {
			return true;
		}
		if ( 'solid' === $style ) {
			return false;
		}
	}

	if ( is_front_page() && riaz_front_page_uses_sections() ) {
		$sections = riaz_get_enabled_sections();
		$overlay  = $sections && 'hero' === $sections[0];
	} elseif ( is_page_template( 'templates/template-homepage.php' ) ) {
		$sections = riaz_get_enabled_sections();
		$overlay  = $sections && 'hero' === $sections[0];
	} elseif ( is_page_template( array( 'templates/template-full-width.php', 'elementor_header_footer', 'elementor_canvas' ) ) ) {
		$overlay = false;
	} elseif ( is_front_page() && ! riaz_front_page_uses_sections() ) {
		$overlay = false;
	} else {
		$overlay = riaz_show_page_banner();
	}

	/**
	 * Filter whether the header overlays a dark top area.
	 *
	 * @param bool $overlay Overlay state.
	 */
	return (bool) apply_filters( 'riaz_header_is_overlay', $overlay );
}

/**
 * Whether the dark page banner is shown on the current inner page.
 *
 * @return bool
 */
function riaz_show_page_banner() {
	if ( ! riaz_mod_bool( 'banner_enable' ) ) {
		return false;
	}
	if ( is_singular() && riaz_meta( get_queried_object_id(), 'hide_banner', '' ) ) {
		return false;
	}
	return true;
}

/**
 * Body classes for header style and motion preferences.
 *
 * @param string[] $classes Classes.
 * @return string[]
 */
function riaz_body_classes( $classes ) {
	$classes[] = riaz_header_is_overlay() ? 'rp-header-overlay' : 'rp-header-solid';

	if ( ! riaz_mod_bool( 'header_sticky' ) ) {
		$classes[] = 'rp-header-static';
	}
	if ( ! riaz_mod_bool( 'anim_enable' ) ) {
		$classes[] = 'rp-no-anim';
	}
	if ( riaz_mod_bool( 'cursor_enable' ) && riaz_mod_bool( 'anim_enable' ) ) {
		$classes[] = 'rp-cursor';
	}
	if ( riaz_mod_bool( 'progress_enable' ) ) {
		$classes[] = 'rp-progress';
	}
	if ( riaz_is_sections_page() ) {
		$classes[] = 'rp-sections-page';
	}
	if ( is_singular() && riaz_is_builder_content( get_queried_object_id() ) ) {
		$classes[] = 'rp-builder-content';
	}
	if ( is_singular( array( 'post', 'riaz_portfolio', 'riaz_case_study', 'riaz_service' ) ) && has_post_thumbnail( get_queried_object_id() ) && riaz_show_page_banner() && ! is_page_template( 'templates/template-full-width.php' ) ) {
		$classes[] = 'has-featured-overlap';
	}
	return $classes;
}
add_filter( 'body_class', 'riaz_body_classes' );

/**
 * Remove the "Category:" style prefix from archive titles; the banner shows it as a label.
 *
 * @param string $title Title.
 * @return string
 */
function riaz_archive_title( $title ) {
	if ( is_category() || is_tag() || is_tax() ) {
		return single_term_title( '', false );
	}
	if ( is_post_type_archive() ) {
		return post_type_archive_title( '', false );
	}
	if ( is_author() ) {
		return get_the_author();
	}
	return $title;
}
add_filter( 'get_the_archive_title', 'riaz_archive_title' );

/**
 * Shorter excerpts for cards.
 *
 * @return int
 */
function riaz_excerpt_length() {
	return 24;
}
add_filter( 'excerpt_length', 'riaz_excerpt_length' );

/**
 * Plain ellipsis instead of "[...]".
 *
 * @return string
 */
function riaz_excerpt_more() {
	return '&hellip;';
}
add_filter( 'excerpt_more', 'riaz_excerpt_more' );

/**
 * Add a pingback header on single posts that accept pingbacks.
 */
function riaz_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">' . "\n", esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'riaz_pingback_header' );

/**
 * In-page anchor menu items ("#about" links) all share the homepage URL, so
 * WordPress marks every one of them as current. Remove that; the scrollspy
 * in main.js highlights the section actually in view.
 *
 * @param string[] $classes Classes.
 * @param WP_Post  $item    Menu item.
 * @return string[]
 */
function riaz_nav_hash_item_classes( $classes, $item ) {
	if ( ! empty( $item->url ) && false !== strpos( $item->url, '#' ) && '#' !== substr( $item->url, -1 ) ) {
		$classes = array_diff( $classes, array( 'current-menu-item', 'current_page_item', 'current-menu-ancestor', 'current_page_parent' ) );
	}
	return $classes;
}
add_filter( 'nav_menu_css_class', 'riaz_nav_hash_item_classes', 10, 2 );

/**
 * Drop aria-current from in-page anchor menu links (see above).
 *
 * @param array   $atts Link attributes.
 * @param WP_Post $item Menu item.
 * @return array
 */
function riaz_nav_hash_item_atts( $atts, $item ) {
	if ( ! empty( $item->url ) && false !== strpos( $item->url, '#' ) && '#' !== substr( $item->url, -1 ) ) {
		unset( $atts['aria-current'] );
	}
	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'riaz_nav_hash_item_atts', 10, 2 );

/**
 * Hide menu links to homepage sections that are switched off or empty
 * (for example "Testimonials" before any testimonial is published).
 *
 * @param WP_Post[] $items Menu items.
 * @return WP_Post[]
 */
function riaz_hide_empty_section_links( $items ) {
	if ( ! riaz_front_page_uses_sections() ) {
		return $items;
	}
	$home_path = untrailingslashit( (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH ) );
	$anchors   = array();
	foreach ( array_keys( riaz_get_sections() ) as $slug ) {
		$anchors[ riaz_section_anchor( $slug ) ] = $slug;
	}
	$enabled = riaz_get_enabled_sections();
	$removed = array();

	foreach ( $items as $key => $item ) {
		$fragment = (string) wp_parse_url( $item->url, PHP_URL_FRAGMENT );
		if ( '' === $fragment || ! isset( $anchors[ $fragment ] ) || 'top' === $fragment ) {
			continue;
		}
		$path = untrailingslashit( (string) wp_parse_url( $item->url, PHP_URL_PATH ) );
		$host = wp_parse_url( $item->url, PHP_URL_HOST );
		if ( ( $host && wp_parse_url( home_url(), PHP_URL_HOST ) !== $host ) || ( '' !== $path && $path !== $home_path ) ) {
			continue;
		}
		$slug = $anchors[ $fragment ];
		if ( ! in_array( $slug, $enabled, true ) || ! riaz_section_has_content( $slug ) ) {
			$removed[] = (int) $item->ID;
			unset( $items[ $key ] );
		}
	}

	// Drop children of removed items too.
	if ( $removed ) {
		foreach ( $items as $key => $item ) {
			if ( in_array( (int) $item->menu_item_parent, $removed, true ) ) {
				unset( $items[ $key ] );
			}
		}
	}
	return array_values( $items );
}
add_filter( 'wp_nav_menu_objects', 'riaz_hide_empty_section_links' );

/**
 * Load core block CSS only for blocks present on the page. Uses the raw
 * theme mod because this filter can run before translations are loaded.
 *
 * @param bool $load Current value.
 * @return bool
 */
function riaz_separate_block_assets( $load ) {
	return get_theme_mod( 'perf_block_css', true ) ? true : $load;
}
add_filter( 'should_load_separate_core_block_assets', 'riaz_separate_block_assets' );
