<?php
/**
 * Template tags: breadcrumbs, post meta, banners, navigation fallbacks.
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * Breadcrumb trail for the current request.
 *
 * @return array[] List of { label, url } (the last item has no URL).
 */
function riaz_breadcrumb_trail() {
	$trail = array(
		array(
			'label' => __( 'Home', 'riaz-portfolio' ),
			'url'   => home_url( '/' ),
		),
	);

	if ( is_front_page() ) {
		return array();
	}

	$posts_page = (int) get_option( 'page_for_posts' );

	if ( is_home() ) {
		$trail[] = array(
			'label' => $posts_page ? get_the_title( $posts_page ) : __( 'Blog', 'riaz-portfolio' ),
			'url'   => '',
		);
	} elseif ( is_singular() ) {
		$post = get_queried_object();
		if ( 'post' === $post->post_type ) {
			if ( $posts_page ) {
				$trail[] = array(
					'label' => get_the_title( $posts_page ),
					'url'   => get_permalink( $posts_page ),
				);
			}
			$categories = get_the_category( $post->ID );
			if ( $categories ) {
				$trail[] = array(
					'label' => $categories[0]->name,
					'url'   => get_category_link( $categories[0] ),
				);
			}
		} elseif ( 'page' === $post->post_type ) {
			foreach ( array_reverse( get_post_ancestors( $post ) ) as $ancestor ) {
				$trail[] = array(
					'label' => get_the_title( $ancestor ),
					'url'   => get_permalink( $ancestor ),
				);
			}
		} else {
			$archive = get_post_type_archive_link( $post->post_type );
			$object  = get_post_type_object( $post->post_type );
			if ( $archive && $object ) {
				$trail[] = array(
					'label' => $object->labels->name,
					'url'   => $archive,
				);
			}
		}
		$trail[] = array(
			'label' => single_post_title( '', false ),
			'url'   => '',
		);
	} elseif ( is_post_type_archive() ) {
		$trail[] = array(
			'label' => post_type_archive_title( '', false ),
			'url'   => '',
		);
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$term = get_queried_object();
		if ( is_tax( 'riaz_portfolio_cat' ) ) {
			$trail[] = array(
				'label' => __( 'Portfolio', 'riaz-portfolio' ),
				'url'   => get_post_type_archive_link( 'riaz_portfolio' ),
			);
		} elseif ( $posts_page ) {
			$trail[] = array(
				'label' => get_the_title( $posts_page ),
				'url'   => get_permalink( $posts_page ),
			);
		}
		if ( $term && ! empty( $term->parent ) ) {
			foreach ( array_reverse( get_ancestors( $term->term_id, $term->taxonomy ) ) as $parent_id ) {
				$parent  = get_term( $parent_id, $term->taxonomy );
				$trail[] = array(
					'label' => $parent->name,
					'url'   => get_term_link( $parent ),
				);
			}
		}
		$trail[] = array(
			'label' => single_term_title( '', false ),
			'url'   => '',
		);
	} elseif ( is_search() ) {
		$trail[] = array(
			/* translators: %s: search query. */
			'label' => sprintf( __( 'Search: %s', 'riaz-portfolio' ), get_search_query() ),
			'url'   => '',
		);
	} elseif ( is_author() ) {
		$trail[] = array(
			'label' => get_the_author_meta( 'display_name', (int) get_query_var( 'author' ) ),
			'url'   => '',
		);
	} elseif ( is_date() ) {
		$trail[] = array(
			'label' => wp_strip_all_tags( get_the_archive_title() ),
			'url'   => '',
		);
	} elseif ( is_404() ) {
		$trail[] = array(
			'label' => __( 'Page not found', 'riaz-portfolio' ),
			'url'   => '',
		);
	}

	/**
	 * Filter the breadcrumb trail.
	 *
	 * @param array $trail Items with label and url.
	 */
	return apply_filters( 'riaz_breadcrumb_trail', $trail );
}

/**
 * Render breadcrumbs.
 */
function riaz_breadcrumbs() {
	if ( ! riaz_mod_bool( 'breadcrumbs_enable' ) ) {
		return;
	}
	$trail = riaz_breadcrumb_trail();
	if ( count( $trail ) < 2 ) {
		return;
	}
	echo '<nav class="breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'riaz-portfolio' ) . '"><ol>';
	$last = count( $trail ) - 1;
	foreach ( $trail as $index => $item ) {
		if ( $index === $last || '' === $item['url'] ) {
			printf( '<li><span aria-current="page">%s</span></li>', esc_html( $item['label'] ) );
		} else {
			printf( '<li><a href="%1$s">%2$s</a></li>', esc_url( $item['url'] ), esc_html( $item['label'] ) );
		}
	}
	echo '</ol></nav>';
}

/**
 * Banner data for the current request: eyebrow, title, text and meta.
 *
 * @return array
 */
function riaz_banner_data() {
	$data = array(
		'eyebrow'  => '',
		'title'    => '',
		'text'     => '',
		'meta'     => false,
		'search'   => false,
		'centered' => false,
	);

	if ( is_home() ) {
		$posts_page      = (int) get_option( 'page_for_posts' );
		$data['eyebrow'] = riaz_mod( 'blog_eyebrow' );
		$data['title']   = $posts_page ? get_the_title( $posts_page ) : __( 'Blog', 'riaz-portfolio' );
		$data['text']    = $posts_page && has_excerpt( $posts_page ) ? get_the_excerpt( $posts_page ) : riaz_mod( 'blog_text' );
	} elseif ( is_singular() ) {
		$post          = get_queried_object();
		$data['title'] = single_post_title( '', false );
		$subtitle      = riaz_meta( $post->ID, 'banner_subtitle' );
		$data['text']  = $subtitle ? $subtitle : ( has_excerpt( $post ) && 'page' !== $post->post_type ? get_the_excerpt( $post ) : '' );

		if ( 'post' === $post->post_type ) {
			$categories      = get_the_category( $post->ID );
			$data['eyebrow'] = $categories ? $categories[0]->name : '';
			$data['meta']    = true;
		} elseif ( 'page' !== $post->post_type ) {
			$object          = get_post_type_object( $post->post_type );
			$data['eyebrow'] = $object ? $object->labels->singular_name : '';
			if ( 'riaz_portfolio' === $post->post_type ) {
				$terms           = get_the_terms( $post->ID, 'riaz_portfolio_cat' );
				$data['eyebrow'] = $terms && ! is_wp_error( $terms ) ? $terms[0]->name : $data['eyebrow'];
			}
		}
	} elseif ( is_search() ) {
		$data['eyebrow'] = __( 'Search results', 'riaz-portfolio' );
		/* translators: %s: search query. */
		$data['title']  = sprintf( __( 'Results for “%s”', 'riaz-portfolio' ), get_search_query() );
		$data['search'] = true;
	} elseif ( is_archive() ) {
		$data['title'] = wp_strip_all_tags( get_the_archive_title() );
		$data['text']  = get_the_archive_description();
		if ( is_category() ) {
			$data['eyebrow'] = __( 'Category', 'riaz-portfolio' );
		} elseif ( is_tag() ) {
			$data['eyebrow'] = __( 'Tag', 'riaz-portfolio' );
		} elseif ( is_author() ) {
			$data['eyebrow'] = __( 'Author', 'riaz-portfolio' );
			$data['text']    = get_the_author_meta( 'description', (int) get_query_var( 'author' ) );
		} elseif ( is_post_type_archive( 'riaz_portfolio' ) || is_tax( 'riaz_portfolio_cat' ) ) {
			$data['eyebrow'] = riaz_mod( 'portfolio_eyebrow' );
			if ( is_post_type_archive() ) {
				$data['text'] = riaz_mod( 'portfolio_text' );
			}
		} elseif ( is_post_type_archive( 'riaz_case_study' ) ) {
			$data['eyebrow'] = riaz_mod( 'cases_eyebrow' );
			$data['text']    = riaz_mod( 'cases_text' );
		} elseif ( is_post_type_archive( 'riaz_service' ) ) {
			$data['eyebrow'] = riaz_mod( 'services_eyebrow' );
			$data['text']    = riaz_mod( 'services_text' );
		} elseif ( is_date() ) {
			$data['eyebrow'] = __( 'Archive', 'riaz-portfolio' );
		}
	} elseif ( is_404() ) {
		$data['centered'] = true;
	}

	/**
	 * Filter the page banner content.
	 *
	 * @param array $data Banner data.
	 */
	return apply_filters( 'riaz_banner_data', $data );
}

/**
 * Post meta line (date, reading time, author).
 *
 * @param bool $with_author Include the author.
 */
function riaz_post_meta( $with_author = false ) {
	?>
	<div class="post-meta">
		<span><?php riaz_icon( 'calendar', 14 ); ?><time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time></span>
		<span><?php riaz_icon( 'clock', 14 ); ?>
		<?php
		/* translators: %d: minutes. */
		echo esc_html( sprintf( _n( '%d min read', '%d min read', riaz_reading_time(), 'riaz-portfolio' ), riaz_reading_time() ) );
		?>
		</span>
		<?php if ( $with_author ) : ?>
			<span><?php riaz_icon( 'user', 14 ); ?><?php the_author(); ?></span>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Card thumbnail with a branded fallback.
 *
 * @param string $size Image size.
 * @param string $link URL the image links to.
 */
function riaz_card_media( $size = 'riaz-card', $link = '' ) {
	$link = $link ? $link : get_permalink();
	?>
	<a class="project-media" href="<?php echo esc_url( $link ); ?>" tabindex="-1" aria-hidden="true">
		<?php
		if ( has_post_thumbnail() ) {
			the_post_thumbnail(
				$size,
				array(
					'alt'     => '',
					'loading' => 'lazy',
					'sizes'   => '(max-width: 600px) 92vw, (max-width: 1100px) 46vw, 400px',
				)
			);
		} else {
			echo '<span class="project-media-fallback">' . riaz_get_icon( 'image', 36, array( 'stroke' => 1.5 ) ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted SVG.
		}
		?>
		<span class="project-arrow"><?php riaz_icon( 'arrow-up-right', 20 ); ?></span>
	</a>
	<?php
}

/**
 * Share links for the current post (no third-party scripts).
 */
function riaz_share_links() {
	$url   = rawurlencode( get_permalink() );
	$title = rawurlencode( wp_strip_all_tags( get_the_title() ) );
	$links = array(
		'linkedin' => array( 'https://www.linkedin.com/sharing/share-offsite/?url=' . $url, 'LinkedIn' ),
		'x'        => array( 'https://x.com/intent/post?url=' . $url . '&text=' . $title, 'X' ),
		'facebook' => array( 'https://www.facebook.com/sharer/sharer.php?u=' . $url, 'Facebook' ),
		'whatsapp' => array( 'https://wa.me/?text=' . $title . '%20' . $url, 'WhatsApp' ),
	);
	?>
	<div class="share-links">
		<span><?php esc_html_e( 'Share', 'riaz-portfolio' ); ?></span>
		<ul>
			<?php foreach ( $links as $network => $link ) : ?>
				<li>
					<a href="<?php echo esc_url( $link[0] ); ?>" target="_blank" rel="noopener noreferrer">
						<?php riaz_icon( $network, 15 ); ?>
						<span class="screen-reader-text">
							<?php
							/* translators: %s: network name. */
							echo esc_html( sprintf( __( 'Share on %s (opens in a new tab)', 'riaz-portfolio' ), $link[1] ) );
							?>
						</span>
					</a>
				</li>
			<?php endforeach; ?>
			<li>
				<button type="button" data-copy-link="<?php echo esc_url( get_permalink() ); ?>" data-copied-label="<?php esc_attr_e( 'Link copied', 'riaz-portfolio' ); ?>">
					<?php riaz_icon( 'link', 15 ); ?>
					<span class="screen-reader-text"><?php esc_html_e( 'Copy link', 'riaz-portfolio' ); ?></span>
				</button>
			</li>
		</ul>
	</div>
	<?php
}

/**
 * Archive pagination.
 */
function riaz_pagination() {
	the_posts_pagination(
		array(
			'mid_size'           => 1,
			'prev_text'          => riaz_get_icon( 'chevron-left', 18 ) . '<span class="screen-reader-text">' . esc_html__( 'Previous page', 'riaz-portfolio' ) . '</span>',
			'next_text'          => '<span class="screen-reader-text">' . esc_html__( 'Next page', 'riaz-portfolio' ) . '</span>' . riaz_get_icon( 'chevron-right', 18 ),
			'before_page_number' => '<span class="screen-reader-text">' . esc_html__( 'Page', 'riaz-portfolio' ) . ' </span>',
			'class'              => 'pagination',
		)
	);
}

/**
 * Anchor-link menu used before a Primary menu is assigned.
 */
function riaz_primary_menu_fallback() {
	$items   = array( 'top' => __( 'Home', 'riaz-portfolio' ) );
	$map     = array(
		'about'        => __( 'About', 'riaz-portfolio' ),
		'services'     => __( 'Services', 'riaz-portfolio' ),
		'approach'     => __( 'Approach', 'riaz-portfolio' ),
		'portfolio'    => __( 'Work', 'riaz-portfolio' ),
		'testimonials' => __( 'Testimonials', 'riaz-portfolio' ),
		'faq'          => __( 'FAQ', 'riaz-portfolio' ),
	);
	$enabled = riaz_get_enabled_sections();
	foreach ( $map as $slug => $label ) {
		if ( in_array( $slug, $enabled, true ) && riaz_section_has_content( $slug ) ) {
			$items[ riaz_section_anchor( $slug ) ] = $label;
		}
	}

	echo '<ul class="menu">';
	foreach ( $items as $anchor => $label ) {
		printf(
			'<li class="menu-item"><a href="%1$s">%2$s</a></li>',
			esc_url( riaz_link_url( '#' . $anchor ) ),
			esc_html( $label )
		);
	}
	$posts_page = (int) get_option( 'page_for_posts' );
	if ( $posts_page ) {
		printf(
			'<li class="menu-item%1$s"><a href="%2$s">%3$s</a></li>',
			is_home() ? ' current-menu-item' : '',
			esc_url( get_permalink( $posts_page ) ),
			esc_html( get_the_title( $posts_page ) )
		);
	}
	echo '</ul>';
}

/**
 * Render a footer menu column with a fallback list.
 *
 * @param string $location Menu location.
 */
function riaz_footer_menu( $location ) {
	if ( has_nav_menu( $location ) ) {
		wp_nav_menu(
			array(
				'theme_location' => $location,
				'container'      => false,
				'depth'          => 1,
				'fallback_cb'    => false,
			)
		);
		return;
	}

	echo '<ul>';
	if ( 'footer_col1' === $location ) {
		$links = array(
			'about'    => __( 'About', 'riaz-portfolio' ),
			'services' => __( 'Services', 'riaz-portfolio' ),
			'approach' => __( 'Approach', 'riaz-portfolio' ),
			'faq'      => __( 'FAQ', 'riaz-portfolio' ),
		);
		foreach ( $links as $anchor => $label ) {
			printf( '<li><a href="%1$s">%2$s</a></li>', esc_url( riaz_link_url( '#' . $anchor ) ), esc_html( $label ) );
		}
	} else {
		$services = get_posts(
			array(
				'post_type'      => 'riaz_service',
				'posts_per_page' => 4,
				'orderby'        => array(
					'menu_order' => 'ASC',
					'date'       => 'DESC',
				),
			)
		);
		foreach ( $services as $service ) {
			printf( '<li><a href="%1$s">%2$s</a></li>', esc_url( riaz_link_url( '#services' ) ), esc_html( get_the_title( $service ) ) );
		}
	}
	echo '</ul>';
}

/**
 * Footer copyright with {year} and {site} placeholders.
 *
 * @return string
 */
function riaz_copyright() {
	$text = (string) riaz_mod( 'footer_copyright' );
	return strtr(
		$text,
		array(
			'{year}' => wp_date( 'Y' ),
			'{site}' => riaz_brand_name(),
		)
	);
}

/**
 * Brand name used in the footer and wordmark.
 *
 * @return string
 */
function riaz_brand_name() {
	$brand = trim( (string) riaz_mod( 'footer_brand' ) );
	return $brand ? $brand : get_bloginfo( 'name' );
}

/**
 * Comment markup.
 *
 * @param WP_Comment $comment Comment.
 * @param array      $args    Arguments.
 * @param int        $depth   Depth.
 */
function riaz_comment( $comment, $args, $depth ) {
	$tag = ( 'div' === $args['style'] ) ? 'div' : 'li';
	?>
	<<?php echo esc_html( $tag ); ?> id="comment-<?php comment_ID(); ?>" <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent', $comment ); ?>>
		<article class="comment-body">
			<footer class="comment-meta">
				<div class="comment-author vcard">
					<?php echo get_avatar( $comment, 40 ); ?>
					<b class="fn"><?php comment_author_link( $comment ); ?></b>
				</div>
				<div class="comment-metadata">
					<a href="<?php echo esc_url( get_comment_link( $comment, $args ) ); ?>">
						<time datetime="<?php comment_time( 'c' ); ?>"><?php comment_date( '', $comment ); ?></time>
					</a>
					<?php edit_comment_link( __( 'Edit', 'riaz-portfolio' ), ' · ' ); ?>
				</div>
			</footer>
			<?php if ( '0' === $comment->comment_approved ) : ?>
				<p class="comment-awaiting-moderation"><?php esc_html_e( 'Your comment is awaiting moderation.', 'riaz-portfolio' ); ?></p>
			<?php endif; ?>
			<div class="comment-content"><?php comment_text(); ?></div>
			<?php
			comment_reply_link(
				array_merge(
					$args,
					array(
						'depth'     => $depth,
						'max_depth' => $args['max_depth'],
						'before'    => '<div class="reply">',
						'after'     => '</div>',
					)
				)
			);
			?>
		</article>
	<?php
}

/**
 * Whether the blog sidebar should be shown on the current view.
 *
 * @return bool
 */
function riaz_has_sidebar() {
	if ( ! riaz_mod_bool( 'blog_sidebar' ) || ! is_active_sidebar( 'sidebar-1' ) ) {
		return false;
	}
	return is_home() || is_singular( 'post' ) || is_category() || is_tag() || is_author() || is_date() || is_search();
}

/**
 * Category links above the portfolio archive (real URLs, crawlable).
 */
function riaz_portfolio_term_filter() {
	$terms = get_terms(
		array(
			'taxonomy'   => 'riaz_portfolio_cat',
			'hide_empty' => true,
		)
	);
	if ( is_wp_error( $terms ) || count( $terms ) < 2 ) {
		return;
	}
	echo '<nav class="term-filter" aria-label="' . esc_attr__( 'Project categories', 'riaz-portfolio' ) . '">';
	printf(
		'<a href="%1$s"%2$s>%3$s</a>',
		esc_url( get_post_type_archive_link( 'riaz_portfolio' ) ),
		is_post_type_archive( 'riaz_portfolio' ) ? ' aria-current="page"' : '',
		esc_html__( 'All', 'riaz-portfolio' )
	);
	foreach ( $terms as $term ) {
		printf(
			'<a href="%1$s"%2$s>%3$s</a>',
			esc_url( get_term_link( $term ) ),
			is_tax( 'riaz_portfolio_cat', $term->term_id ) ? ' aria-current="page"' : '',
			esc_html( $term->name )
		);
	}
	echo '</nav>';
}
