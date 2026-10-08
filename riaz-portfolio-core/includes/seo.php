<?php
/**
 * Lightweight SEO: meta description, Open Graph, Twitter cards, JSON-LD
 * schema and optional Google Analytics 4.
 *
 * Everything except analytics steps aside automatically when a dedicated
 * SEO plugin is active, so markup is never duplicated.
 *
 * @package Riaz_Portfolio_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether a known SEO plugin is handling meta tags and schema.
 *
 * @return bool
 */
function riaz_core_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' )
		|| defined( 'RANK_MATH_VERSION' )
		|| defined( 'SEOPRESS_VERSION' )
		|| defined( 'AIOSEO_VERSION' )
		|| defined( 'THE_SEO_FRAMEWORK_VERSION' )
		|| defined( 'SLIM_SEO_VER' );
}

/**
 * Whether the theme should print its own SEO tags.
 *
 * @return bool
 */
function riaz_core_seo_enabled() {
	/**
	 * Filter whether the built-in SEO output runs.
	 *
	 * @param bool $enabled Enabled state.
	 */
	return (bool) apply_filters( 'riaz_core_seo_enabled', (bool) riaz_core_option( 'seo_enable' ) && ! riaz_core_seo_plugin_active() );
}

/**
 * Canonical URL of the current view.
 *
 * @return string
 */
function riaz_core_current_url() {
	if ( is_front_page() ) {
		return home_url( '/' );
	}
	if ( is_singular() ) {
		$canonical = wp_get_canonical_url();
		return $canonical ? $canonical : get_permalink();
	}
	$paged = max( 1, (int) get_query_var( 'paged' ) );
	if ( is_home() || is_archive() || is_search() ) {
		return get_pagenum_link( $paged );
	}
	global $wp;
	return home_url( isset( $wp->request ) && $wp->request ? trailingslashit( $wp->request ) : '/' );
}

/**
 * Plain-text description for the current view.
 *
 * @return string
 */
function riaz_core_seo_description() {
	$text = '';
	if ( is_front_page() ) {
		$text = riaz_core_option( 'seo_description' );
		if ( ! $text && is_singular() && has_excerpt() ) {
			$text = get_the_excerpt();
		}
	} elseif ( is_singular() ) {
		$post = get_queried_object();
		$text = riaz_core_meta( $post->ID, 'banner_subtitle' );
		if ( ! $text ) {
			$text = has_excerpt( $post ) ? $post->post_excerpt : excerpt_remove_blocks( strip_shortcodes( $post->post_content ) );
		}
	} elseif ( is_home() ) {
		$text = riaz_core_theme_text( 'blog_text' );
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$text = term_description();
	} elseif ( is_post_type_archive( 'riaz_portfolio' ) ) {
		$text = riaz_core_theme_text( 'portfolio_text' );
	} elseif ( is_post_type_archive( 'riaz_case_study' ) ) {
		$text = riaz_core_theme_text( 'cases_text' );
	} elseif ( is_post_type_archive( 'riaz_service' ) ) {
		$text = riaz_core_theme_text( 'services_text' );
	} elseif ( is_author() ) {
		$text = get_the_author_meta( 'description', (int) get_query_var( 'author' ) );
	}

	if ( ! $text ) {
		$text = get_bloginfo( 'description' );
	}
	$text = trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( (string) $text ) ) );
	return wp_trim_words( $text, 32, '…' );
}

/**
 * Share image for the current view: [ url, width, height ].
 *
 * @return array
 */
function riaz_core_seo_image() {
	if ( is_singular() && has_post_thumbnail( get_queried_object_id() ) ) {
		$image = wp_get_attachment_image_src( get_post_thumbnail_id( get_queried_object_id() ), 'riaz-wide' );
		if ( $image ) {
			return array( $image[0], $image[1], $image[2] );
		}
	}
	$id = absint( riaz_core_option( 'seo_share_image' ) );
	if ( $id ) {
		$image = wp_get_attachment_image_src( $id, 'full' );
		if ( $image ) {
			return array( $image[0], $image[1], $image[2] );
		}
	}
	/**
	 * Fallback share image as [ url, width, height ] (the theme supplies one).
	 *
	 * @param array $image Image data or empty array.
	 */
	return (array) apply_filters( 'riaz_core_default_share_image', array() );
}

/**
 * Print meta description, Open Graph and Twitter tags.
 */
function riaz_core_seo_meta_tags() {
	if ( ! riaz_core_seo_enabled() || is_404() ) {
		return;
	}
	$title       = wp_get_document_title();
	$description = riaz_core_seo_description();
	$url         = riaz_core_current_url();
	$image       = riaz_core_seo_image();
	$is_article  = is_singular( 'post' );

	$tags = array(
		array( 'name', 'description', $description ),
		array( 'property', 'og:locale', str_replace( '-', '_', get_bloginfo( 'language' ) ) ),
		array( 'property', 'og:type', $is_article ? 'article' : 'website' ),
		array( 'property', 'og:title', $title ),
		array( 'property', 'og:description', $description ),
		array( 'property', 'og:url', $url ),
		array( 'property', 'og:site_name', get_bloginfo( 'name' ) ),
		array( 'property', 'og:image', isset( $image[0] ) ? $image[0] : '' ),
		array( 'property', 'og:image:width', isset( $image[1] ) ? $image[1] : '' ),
		array( 'property', 'og:image:height', isset( $image[2] ) ? $image[2] : '' ),
		array( 'name', 'twitter:card', 'summary_large_image' ),
		array( 'name', 'twitter:title', $title ),
		array( 'name', 'twitter:description', $description ),
		array( 'name', 'twitter:image', isset( $image[0] ) ? $image[0] : '' ),
	);

	if ( $is_article ) {
		$tags[] = array( 'property', 'article:published_time', get_the_date( DATE_W3C ) );
		$tags[] = array( 'property', 'article:modified_time', get_the_modified_date( DATE_W3C ) );
	}

	$handle = ltrim( trim( (string) riaz_core_option( 'seo_x_handle' ) ), '@' );
	if ( $handle ) {
		$tags[] = array( 'name', 'twitter:site', '@' . $handle );
	}

	echo "\n<!-- Riaz Portfolio SEO -->\n";
	foreach ( $tags as $tag ) {
		if ( '' === (string) $tag[2] ) {
			continue;
		}
		printf( '<meta %1$s="%2$s" content="%3$s">' . "\n", esc_attr( $tag[0] ), esc_attr( $tag[1] ), esc_attr( $tag[2] ) );
	}

	// Core prints canonical links for singular views only.
	if ( ! is_singular() ) {
		printf( '<link rel="canonical" href="%s">' . "\n", esc_url( $url ) );
	}
}
add_action( 'wp_head', 'riaz_core_seo_meta_tags', 3 );

/**
 * Print the JSON-LD schema graph.
 */
function riaz_core_schema_output() {
	if ( ! riaz_core_seo_enabled() || is_404() ) {
		return;
	}
	$graph = riaz_core_schema_graph();
	if ( ! $graph ) {
		return;
	}
	$json = wp_json_encode(
		array(
			'@context' => 'https://schema.org',
			'@graph'   => $graph,
		),
		JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP
	);
	echo '<script type="application/ld+json">' . $json . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON with HEX_TAG.
}
add_action( 'wp_head', 'riaz_core_schema_output', 20 );

/**
 * Build the schema graph for the current view.
 *
 * @return array
 */
function riaz_core_schema_graph() {
	$home     = home_url( '/' );
	$identity = $home . '#identity';
	$website  = $home . '#website';
	$url      = riaz_core_current_url();
	$language = get_bloginfo( 'language' );
	$name     = trim( (string) riaz_core_option( 'seo_entity_name' ) );
	$name     = $name ? $name : get_bloginfo( 'name' );
	/**
	 * Public profile URLs for the schema "sameAs" list (the theme supplies its social links).
	 *
	 * @param string[] $urls Profile URLs.
	 */
	$same_as = array_values( array_filter( (array) apply_filters( 'riaz_core_same_as', array() ) ) );

	$graph = array();

	$graph[] = array(
		'@type'           => 'WebSite',
		'@id'             => $website,
		'url'             => $home,
		'name'            => get_bloginfo( 'name' ),
		'description'     => get_bloginfo( 'description' ),
		'inLanguage'      => $language,
		'publisher'       => array( '@id' => $identity ),
		'potentialAction' => array(
			'@type'       => 'SearchAction',
			'target'      => array(
				'@type'       => 'EntryPoint',
				'urlTemplate' => home_url( '/?s={search_term_string}' ),
			),
			'query-input' => 'required name=search_term_string',
		),
	);

	$logo_id = (int) get_theme_mod( 'custom_logo' );
	/**
	 * Image representing the person (the theme supplies its hero portrait).
	 *
	 * @param string $url Image URL.
	 */
	$portrait = (string) apply_filters( 'riaz_core_entity_image', $logo_id ? wp_get_attachment_image_url( $logo_id, 'full' ) : '' );

	if ( 'organization' === riaz_core_option( 'seo_entity' ) ) {
		$entity = array(
			'@type' => 'Organization',
			'@id'   => $identity,
			'name'  => $name,
			'url'   => $home,
		);
		if ( $logo_id ) {
			$entity['logo'] = wp_get_attachment_image_url( $logo_id, 'full' );
		}
	} else {
		$entity   = array(
			'@type'       => 'Person',
			'@id'         => $identity,
			'name'        => $name,
			'url'         => $home,
			'image'       => $portrait,
			'jobTitle'    => riaz_core_option( 'seo_job_title' ),
			'description' => riaz_core_option( 'seo_description' ),
		);
		$services = get_posts(
			array(
				'post_type'      => 'riaz_service',
				'posts_per_page' => 8,
				'fields'         => 'ids',
				'orderby'        => array(
					'menu_order' => 'ASC',
					'date'       => 'DESC',
				),
			)
		);
		if ( $services ) {
			$entity['knowsAbout'] = array_map( 'get_the_title', $services );
		}
	}
	if ( $same_as ) {
		$entity['sameAs'] = $same_as;
	}
	$graph[] = array_filter( $entity );

	$type = 'WebPage';
	if ( is_search() ) {
		$type = 'SearchResultsPage';
	} elseif ( is_home() || is_archive() ) {
		$type = 'CollectionPage';
	}
	$webpage = array(
		'@type'       => $type,
		'@id'         => $url . '#webpage',
		'url'         => $url,
		'name'        => wp_get_document_title(),
		'description' => riaz_core_seo_description(),
		'isPartOf'    => array( '@id' => $website ),
		'inLanguage'  => $language,
	);
	if ( is_front_page() ) {
		$webpage['about'] = array( '@id' => $identity );
	}

	$trail = function_exists( 'riaz_breadcrumb_trail' ) ? riaz_breadcrumb_trail() : array();
	if ( count( $trail ) > 1 ) {
		$items = array();
		foreach ( $trail as $index => $item ) {
			$items[] = array(
				'@type'    => 'ListItem',
				'position' => $index + 1,
				'name'     => wp_strip_all_tags( $item['label'] ),
				'item'     => $item['url'] ? $item['url'] : $url,
			);
		}
		$graph[]               = array(
			'@type'           => 'BreadcrumbList',
			'@id'             => $url . '#breadcrumb',
			'itemListElement' => $items,
		);
		$webpage['breadcrumb'] = array( '@id' => $url . '#breadcrumb' );
	}

	if ( is_singular() && has_post_thumbnail( get_queried_object_id() ) ) {
		$webpage['primaryImageOfPage'] = get_the_post_thumbnail_url( get_queried_object_id(), 'riaz-wide' );
	}
	$graph[] = array_filter( $webpage );

	if ( is_singular() ) {
		$post    = get_queried_object();
		$image   = has_post_thumbnail( $post ) ? get_the_post_thumbnail_url( $post, 'riaz-wide' ) : '';
		$excerpt = riaz_core_seo_description();

		if ( 'post' === $post->post_type ) {
			$categories = get_the_category( $post->ID );
			$tags       = get_the_tags( $post->ID );
			$graph[]    = array_filter(
				array(
					'@type'            => 'BlogPosting',
					'@id'              => $url . '#article',
					'headline'         => wp_strip_all_tags( get_the_title( $post ) ),
					'description'      => $excerpt,
					'datePublished'    => get_the_date( DATE_W3C, $post ),
					'dateModified'     => get_the_modified_date( DATE_W3C, $post ),
					'author'           => array(
						'@type' => 'Person',
						'name'  => get_the_author_meta( 'display_name', $post->post_author ),
						'url'   => get_author_posts_url( $post->post_author ),
					),
					'publisher'        => array( '@id' => $identity ),
					'mainEntityOfPage' => array( '@id' => $url . '#webpage' ),
					'image'            => $image,
					'articleSection'   => $categories ? $categories[0]->name : '',
					'keywords'         => $tags ? implode( ', ', wp_list_pluck( $tags, 'name' ) ) : '',
					'wordCount'        => str_word_count( wp_strip_all_tags( $post->post_content ) ),
					'inLanguage'       => $language,
				)
			);
		} elseif ( 'riaz_portfolio' === $post->post_type ) {
			$graph[] = array_filter(
				array(
					'@type'       => 'CreativeWork',
					'@id'         => $url . '#work',
					'name'        => wp_strip_all_tags( get_the_title( $post ) ),
					'description' => $excerpt,
					'url'         => $url,
					'image'       => $image,
					'creator'     => array( '@id' => $identity ),
					'dateCreated' => get_the_date( DATE_W3C, $post ),
				)
			);
		} elseif ( 'riaz_case_study' === $post->post_type ) {
			$graph[] = array_filter(
				array(
					'@type'            => 'Article',
					'@id'              => $url . '#article',
					'headline'         => wp_strip_all_tags( get_the_title( $post ) ),
					'description'      => $excerpt,
					'datePublished'    => get_the_date( DATE_W3C, $post ),
					'dateModified'     => get_the_modified_date( DATE_W3C, $post ),
					'author'           => array( '@id' => $identity ),
					'publisher'        => array( '@id' => $identity ),
					'mainEntityOfPage' => array( '@id' => $url . '#webpage' ),
					'image'            => $image,
				)
			);
		} elseif ( 'riaz_service' === $post->post_type ) {
			$graph[] = array_filter(
				array(
					'@type'       => 'Service',
					'@id'         => $url . '#service',
					'name'        => wp_strip_all_tags( get_the_title( $post ) ),
					'description' => $excerpt,
					'url'         => $url,
					'provider'    => array( '@id' => $identity ),
				)
			);
		}
	}

	/**
	 * Whether the current page shows the FAQ list (the theme decides).
	 *
	 * @param bool $show Default: front page only.
	 */
	if ( apply_filters( 'riaz_core_faq_schema', is_front_page() ) ) {
		$faqs = get_posts(
			array(
				'post_type'      => 'riaz_faq',
				'posts_per_page' => 30,
				'orderby'        => array(
					'menu_order' => 'ASC',
					'date'       => 'DESC',
				),
			)
		);
		if ( $faqs ) {
			$questions = array();
			foreach ( $faqs as $faq ) {
				$questions[] = array(
					'@type'          => 'Question',
					'name'           => wp_strip_all_tags( get_the_title( $faq ) ),
					'acceptedAnswer' => array(
						'@type' => 'Answer',
						'text'  => wp_strip_all_tags( excerpt_remove_blocks( $faq->post_content ) ),
					),
				);
			}
			$graph[] = array(
				'@type'      => 'FAQPage',
				'@id'        => $url . '#faq',
				'mainEntity' => $questions,
			);
		}
	}

	/**
	 * Filter the schema graph before output.
	 *
	 * @param array $graph Schema nodes.
	 */
	return apply_filters( 'riaz_core_schema_graph', $graph );
}

/**
 * Google Analytics 4 (skipped in the Customizer and for logged-in editors).
 */
function riaz_core_ga4() {
	$id = riaz_core_option( 'ga4_id' );
	if ( ! $id || is_customize_preview() ) {
		return;
	}
	/**
	 * Filter whether logged-in editors are excluded from analytics.
	 *
	 * @param bool $skip Skip editors.
	 */
	if ( current_user_can( 'edit_posts' ) && apply_filters( 'riaz_core_ga4_skip_editors', true ) ) {
		return;
	}
	wp_print_script_tag(
		array(
			'src'   => 'https://www.googletagmanager.com/gtag/js?id=' . rawurlencode( $id ),
			'async' => true,
		)
	);
	wp_print_inline_script_tag( 'window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag("js",new Date());gtag("config",' . wp_json_encode( $id ) . ');' );
}
add_action( 'wp_head', 'riaz_core_ga4', 30 );

/**
 * Read a text option from the Riaz Portfolio theme, if it is active.
 *
 * @param string $key Theme option key.
 * @return string
 */
function riaz_core_theme_text( $key ) {
	return function_exists( 'riaz_mod' ) ? (string) riaz_mod( $key ) : '';
}
