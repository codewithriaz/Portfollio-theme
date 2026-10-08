<?php
/**
 * Custom post types and taxonomies.
 *
 * Content lives in this plugin, so it survives a change of theme.
 *
 * @package Riaz_Portfolio_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Post type definitions.
 *
 * @return array
 */
function riaz_core_post_type_definitions() {
	return array(
		'riaz_service'     => array(
			'singular' => __( 'Service', 'riaz-portfolio-core' ),
			'plural'   => __( 'Services', 'riaz-portfolio-core' ),
			'slug'     => _x( 'services', 'URL slug', 'riaz-portfolio-core' ),
			'icon'     => 'dashicons-lightbulb',
			'public'   => true,
			'archive'  => true,
			'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'revisions' ),
		),
		'riaz_portfolio'   => array(
			'singular' => __( 'Project', 'riaz-portfolio-core' ),
			'plural'   => __( 'Portfolio', 'riaz-portfolio-core' ),
			'slug'     => _x( 'portfolio', 'URL slug', 'riaz-portfolio-core' ),
			'icon'     => 'dashicons-portfolio',
			'public'   => true,
			'archive'  => true,
			'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'revisions' ),
		),
		'riaz_case_study'  => array(
			'singular' => __( 'Case Study', 'riaz-portfolio-core' ),
			'plural'   => __( 'Case Studies', 'riaz-portfolio-core' ),
			'slug'     => _x( 'case-studies', 'URL slug', 'riaz-portfolio-core' ),
			'icon'     => 'dashicons-chart-line',
			'public'   => true,
			'archive'  => true,
			'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'revisions' ),
		),
		'riaz_testimonial' => array(
			'singular' => __( 'Testimonial', 'riaz-portfolio-core' ),
			'plural'   => __( 'Testimonials', 'riaz-portfolio-core' ),
			'icon'     => 'dashicons-format-quote',
			'public'   => false,
			'supports' => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
		),
		'riaz_team'        => array(
			'singular' => __( 'Team Member', 'riaz-portfolio-core' ),
			'plural'   => __( 'Team', 'riaz-portfolio-core' ),
			'icon'     => 'dashicons-groups',
			'public'   => false,
			'supports' => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
		),
		'riaz_faq'         => array(
			'singular' => __( 'FAQ', 'riaz-portfolio-core' ),
			'plural'   => __( 'FAQs', 'riaz-portfolio-core' ),
			'icon'     => 'dashicons-editor-help',
			'public'   => false,
			'supports' => array( 'title', 'editor', 'page-attributes' ),
		),
		'riaz_pricing'     => array(
			'singular' => __( 'Pricing Plan', 'riaz-portfolio-core' ),
			'plural'   => __( 'Pricing', 'riaz-portfolio-core' ),
			'icon'     => 'dashicons-tag',
			'public'   => false,
			'supports' => array( 'title', 'excerpt', 'page-attributes' ),
		),
	);
}

/**
 * Register post types and taxonomies.
 */
function riaz_core_register_post_types() {
	$position = 21;
	foreach ( riaz_core_post_type_definitions() as $post_type => $def ) {
		$labels = array(
			'name'                  => $def['plural'],
			'singular_name'         => $def['singular'],
			'menu_name'             => $def['plural'],
			/* translators: %s: singular post type name. */
			'add_new_item'          => sprintf( __( 'Add New %s', 'riaz-portfolio-core' ), $def['singular'] ),
			/* translators: %s: singular post type name. */
			'edit_item'             => sprintf( __( 'Edit %s', 'riaz-portfolio-core' ), $def['singular'] ),
			/* translators: %s: singular post type name. */
			'new_item'              => sprintf( __( 'New %s', 'riaz-portfolio-core' ), $def['singular'] ),
			/* translators: %s: singular post type name. */
			'view_item'             => sprintf( __( 'View %s', 'riaz-portfolio-core' ), $def['singular'] ),
			/* translators: %s: plural post type name. */
			'search_items'          => sprintf( __( 'Search %s', 'riaz-portfolio-core' ), $def['plural'] ),
			/* translators: %s: plural post type name. */
			'all_items'             => sprintf( __( 'All %s', 'riaz-portfolio-core' ), $def['plural'] ),
			'not_found'             => __( 'Nothing found.', 'riaz-portfolio-core' ),
			'not_found_in_trash'    => __( 'Nothing found in Trash.', 'riaz-portfolio-core' ),
			'featured_image'        => 'riaz_testimonial' === $post_type || 'riaz_team' === $post_type ? __( 'Photo', 'riaz-portfolio-core' ) : __( 'Featured image', 'riaz-portfolio-core' ),
			'set_featured_image'    => 'riaz_testimonial' === $post_type || 'riaz_team' === $post_type ? __( 'Set photo', 'riaz-portfolio-core' ) : __( 'Set featured image', 'riaz-portfolio-core' ),
			/* translators: %s: plural post type name. */
			'items_list'            => sprintf( __( '%s list', 'riaz-portfolio-core' ), $def['plural'] ),
			'item_published'        => __( 'Published.', 'riaz-portfolio-core' ),
			'item_updated'          => __( 'Updated.', 'riaz-portfolio-core' ),
			'filter_items_list'     => __( 'Filter list', 'riaz-portfolio-core' ),
			'items_list_navigation' => __( 'List navigation', 'riaz-portfolio-core' ),
		);

		$args = array(
			'labels'              => $labels,
			'public'              => $def['public'],
			'publicly_queryable'  => $def['public'],
			'exclude_from_search' => ! $def['public'],
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => $def['public'],
			'show_in_rest'        => true,
			'menu_icon'           => $def['icon'],
			'menu_position'       => $position++,
			'supports'            => $def['supports'],
			'has_archive'         => ! empty( $def['archive'] ) ? $def['slug'] : false,
			'rewrite'             => $def['public'] ? array(
				'slug'       => $def['slug'],
				'with_front' => false,
			) : false,
			'hierarchical'        => false,
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
		);

		/**
		 * Filter the arguments of a theme post type.
		 *
		 * @param array  $args      register_post_type() arguments.
		 * @param string $post_type Post type key.
		 */
		register_post_type( $post_type, apply_filters( 'riaz_core_post_type_args', $args, $post_type ) );
	}

	register_taxonomy(
		'riaz_portfolio_cat',
		array( 'riaz_portfolio' ),
		array(
			'labels'            => array(
				'name'          => __( 'Project Categories', 'riaz-portfolio-core' ),
				'singular_name' => __( 'Project Category', 'riaz-portfolio-core' ),
				'menu_name'     => __( 'Categories', 'riaz-portfolio-core' ),
				'add_new_item'  => __( 'Add New Category', 'riaz-portfolio-core' ),
				'edit_item'     => __( 'Edit Category', 'riaz-portfolio-core' ),
				'all_items'     => __( 'All Categories', 'riaz-portfolio-core' ),
				'search_items'  => __( 'Search Categories', 'riaz-portfolio-core' ),
			),
			'hierarchical'      => true,
			'public'            => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array(
				'slug'       => _x( 'portfolio-category', 'URL slug', 'riaz-portfolio-core' ),
				'with_front' => false,
			),
		)
	);

	// Builders: let Elementor edit the public content types.
	foreach ( array( 'riaz_portfolio', 'riaz_case_study', 'riaz_service' ) as $post_type ) {
		add_post_type_support( $post_type, 'elementor' );
	}
}
add_action( 'init', 'riaz_core_register_post_types' );

/**
 * Sort non-public lists by manual order in the admin.
 *
 * @param WP_Query $query Query.
 */
function riaz_core_admin_order( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() || $query->get( 'orderby' ) ) {
		return;
	}
	$post_type = $query->get( 'post_type' );
	if ( in_array( $post_type, array( 'riaz_service', 'riaz_faq', 'riaz_pricing', 'riaz_team', 'riaz_testimonial' ), true ) ) {
		$query->set(
			'orderby',
			array(
				'menu_order' => 'ASC',
				'date'       => 'DESC',
			)
		);
	}
}
add_action( 'pre_get_posts', 'riaz_core_admin_order' );

/**
 * Order public archives the same way as the homepage sections.
 *
 * @param WP_Query $query Query.
 */
function riaz_core_archive_order( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}
	if ( $query->is_post_type_archive( array( 'riaz_service', 'riaz_portfolio', 'riaz_case_study' ) ) || $query->is_tax( 'riaz_portfolio_cat' ) ) {
		$query->set(
			'orderby',
			array(
				'menu_order' => 'ASC',
				'date'       => 'DESC',
			)
		);
		$query->set( 'posts_per_page', 12 );
	}
}
add_action( 'pre_get_posts', 'riaz_core_archive_order' );

/**
 * Helpful title placeholders.
 *
 * @param string  $text Placeholder.
 * @param WP_Post $post Post.
 * @return string
 */
function riaz_core_title_placeholder( $text, $post ) {
	$placeholders = array(
		'riaz_testimonial' => __( 'Client name', 'riaz-portfolio-core' ),
		'riaz_team'        => __( 'Full name', 'riaz-portfolio-core' ),
		'riaz_faq'         => __( 'Question', 'riaz-portfolio-core' ),
		'riaz_pricing'     => __( 'Plan name', 'riaz-portfolio-core' ),
		'riaz_service'     => __( 'Service name', 'riaz-portfolio-core' ),
		'riaz_portfolio'   => __( 'Project title', 'riaz-portfolio-core' ),
		'riaz_case_study'  => __( 'Case study title', 'riaz-portfolio-core' ),
	);
	return isset( $placeholders[ $post->post_type ] ) ? $placeholders[ $post->post_type ] : $text;
}
add_filter( 'enter_title_here', 'riaz_core_title_placeholder', 10, 2 );

/**
 * Add an "Order" and "Details" column to the theme's lists.
 *
 * @param array $columns Columns.
 * @return array
 */
function riaz_core_admin_columns( $columns ) {
	$new = array();
	foreach ( $columns as $key => $label ) {
		$new[ $key ] = $label;
		if ( 'title' === $key ) {
			$new['riaz_details'] = __( 'Details', 'riaz-portfolio-core' );
			$new['riaz_order']   = __( 'Order', 'riaz-portfolio-core' );
		}
	}
	return $new;
}

/**
 * Render the custom admin columns.
 *
 * @param string $column  Column key.
 * @param int    $post_id Post ID.
 */
function riaz_core_admin_column_content( $column, $post_id ) {
	if ( 'riaz_order' === $column ) {
		echo esc_html( (string) get_post_field( 'menu_order', $post_id ) );
		return;
	}
	if ( 'riaz_details' !== $column ) {
		return;
	}
	$details = array();
	switch ( get_post_type( $post_id ) ) {
		case 'riaz_service':
			if ( riaz_core_meta( $post_id, 'featured' ) ) {
				$details[] = __( 'Highlighted', 'riaz-portfolio-core' );
			}
			$details[] = riaz_core_meta( $post_id, 'icon', 'sparkles' );
			break;
		case 'riaz_testimonial':
			$details[] = riaz_core_youtube_id( riaz_core_meta( $post_id, 'youtube' ) ) ? __( 'Video', 'riaz-portfolio-core' ) : __( 'Text', 'riaz-portfolio-core' );
			$details[] = riaz_core_meta( $post_id, 'role' );
			break;
		case 'riaz_team':
			$details[] = riaz_core_meta( $post_id, 'role' );
			break;
		case 'riaz_pricing':
			$details[] = riaz_core_meta( $post_id, 'price' ) . ' ' . riaz_core_meta( $post_id, 'period' );
			if ( riaz_core_meta( $post_id, 'featured' ) ) {
				$details[] = __( 'Highlighted', 'riaz-portfolio-core' );
			}
			break;
		case 'riaz_portfolio':
		case 'riaz_case_study':
			$details[] = riaz_core_meta( $post_id, 'client' );
			break;
	}
	echo esc_html( implode( ' · ', array_filter( $details ) ) );
}

foreach ( array_keys( riaz_core_post_type_keys() ) as $riaz_core_pt ) {
	add_filter( "manage_{$riaz_core_pt}_posts_columns", 'riaz_core_admin_columns' );
	add_action( "manage_{$riaz_core_pt}_posts_custom_column", 'riaz_core_admin_column_content', 10, 2 );
}
unset( $riaz_core_pt );

/**
 * Post type keys without translated labels (safe to call before init).
 *
 * @return array<string,bool>
 */
function riaz_core_post_type_keys() {
	return array_fill_keys( array( 'riaz_service', 'riaz_portfolio', 'riaz_case_study', 'riaz_testimonial', 'riaz_team', 'riaz_faq', 'riaz_pricing' ), true );
}
