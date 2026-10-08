<?php
/**
 * Starter content importer.
 *
 * Creates the services, FAQs, pages, menus and logo from the original design
 * so a fresh install looks like the demo. It never overwrites existing
 * content and can be run more than once safely. Optional sample portfolio,
 * case study, team and pricing entries are created as drafts so nothing
 * unverified is ever published.
 *
 * @package Riaz_Portfolio_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Starter services from the design.
 *
 * @return array[]
 */
function riaz_core_starter_services() {
	return array(
		array( 'search', __( 'SEO & discoverability', 'riaz-portfolio-core' ), __( 'A solid search foundation with clear priorities and steady improvements.', 'riaz-portfolio-core' ) ),
		array( 'target', __( 'Keyword & audience research', 'riaz-portfolio-core' ), __( 'Understand what your customers search for and the questions they ask.', 'riaz-portfolio-core' ) ),
		array( 'settings-2', __( 'On-page & technical SEO', 'riaz-portfolio-core' ), __( 'Make your site easier for people and search engines to find and understand.', 'riaz-portfolio-core' ) ),
		array( 'pen-line', __( 'Content that connects', 'riaz-portfolio-core' ), __( 'Useful content built from real expertise that supports the customer journey.', 'riaz-portfolio-core' ) ),
		array( 'trending-up', __( 'Digital growth strategy', 'riaz-portfolio-core' ), __( 'Bring channels, audience needs, and business goals into one practical plan.', 'riaz-portfolio-core' ) ),
		array( 'chart-line', __( 'Measurement & learning', 'riaz-portfolio-core' ), __( 'Track what matters, review what works, and keep building momentum.', 'riaz-portfolio-core' ) ),
	);
}

/**
 * Starter FAQs from the design.
 *
 * @return array[]
 */
function riaz_core_starter_faqs() {
	return array(
		array( __( 'What can you help me with?', 'riaz-portfolio-core' ), __( 'Digital strategy, SEO, and content: understanding your audience, improving how you show up in search, and shaping a plan you can act on.', 'riaz-portfolio-core' ) ),
		array( __( 'How do we get started?', 'riaz-portfolio-core' ), __( 'It starts with a conversation about your business, your goals, and where growth feels stuck. From there we shape a focused next step.', 'riaz-portfolio-core' ) ),
		array( __( 'Do you work with startups and small businesses?', 'riaz-portfolio-core' ), __( 'Yes. The approach is shaped around where you are today and where you want to go, whatever the size of the business.', 'riaz-portfolio-core' ) ),
		array( __( 'Is the work focused on measurable outcomes?', 'riaz-portfolio-core' ), __( 'Every recommendation is tied back to a goal, with meaningful measurement built in so you can see what is working.', 'riaz-portfolio-core' ) ),
		array( __( 'What makes your approach different?', 'riaz-portfolio-core' ), __( 'Growth built with intention, not noise. The work starts by understanding your audience and ends with a plan you can actually move forward with.', 'riaz-portfolio-core' ) ),
	);
}

/**
 * Find a post by exact title and type.
 *
 * @param string $title     Title.
 * @param string $post_type Post type.
 * @return int Post ID or 0.
 */
function riaz_core_find_post( $title, $post_type ) {
	$found = get_posts(
		array(
			'post_type'              => $post_type,
			'title'                  => $title,
			'post_status'            => array( 'publish', 'draft', 'pending', 'private', 'future' ),
			'posts_per_page'         => 1,
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
		)
	);
	return $found ? (int) $found[0] : 0;
}

/**
 * Insert a post unless one with the same title exists.
 *
 * @param array $postarr wp_insert_post() arguments.
 * @return int Post ID (existing or new), 0 on failure.
 */
function riaz_core_insert_once( $postarr ) {
	$existing = riaz_core_find_post( $postarr['post_title'], $postarr['post_type'] );
	if ( $existing ) {
		return $existing;
	}
	$postarr['meta_input']               = isset( $postarr['meta_input'] ) ? $postarr['meta_input'] : array();
	$postarr['meta_input']['_riaz_demo'] = 1;
	$id                                  = wp_insert_post( wp_slash( $postarr ), true );
	return is_wp_error( $id ) ? 0 : (int) $id;
}

/**
 * Copy a bundled image into the media library.
 *
 * @param string $file  File name in assets/images.
 * @param string $title Attachment title / alt text.
 * @return int Attachment ID or 0.
 */
function riaz_core_sideload_bundled_image( $file, $title ) {
	$existing = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'meta_key'       => '_riaz_bundled', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- one-off admin action.
			'meta_value'     => $file, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- one-off admin action.
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);
	if ( $existing ) {
		return (int) $existing[0];
	}

	// Images ship with the Riaz Portfolio theme.
	$path = get_theme_file_path( 'assets/images/' . $file );
	if ( ! file_exists( $path ) ) {
		return 0;
	}
	$upload = wp_upload_bits( wp_basename( $path ), null, file_get_contents( $path ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local theme file.
	if ( ! empty( $upload['error'] ) ) {
		return 0;
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';

	$filetype = wp_check_filetype( $upload['file'] );
	$id       = wp_insert_attachment(
		array(
			'post_title'     => $title,
			'post_mime_type' => $filetype['type'],
			'post_status'    => 'inherit',
		),
		$upload['file']
	);
	if ( is_wp_error( $id ) || ! $id ) {
		return 0;
	}
	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $upload['file'] ) );
	update_post_meta( $id, '_wp_attachment_image_alt', $title );
	update_post_meta( $id, '_riaz_bundled', $file );
	return (int) $id;
}

/**
 * Create a menu with custom links if it does not exist yet.
 *
 * @param string $name     Menu name.
 * @param array  $items    List of [ title, url ].
 * @param string $location Theme location to assign.
 * @return int Menu ID.
 */
function riaz_core_create_menu( $name, $items, $location ) {
	$menu = wp_get_nav_menu_object( $name );
	if ( $menu ) {
		$menu_id = (int) $menu->term_id;
	} else {
		$menu_id = wp_create_nav_menu( $name );
		if ( is_wp_error( $menu_id ) ) {
			return 0;
		}
		foreach ( $items as $position => $item ) {
			wp_update_nav_menu_item(
				$menu_id,
				0,
				array(
					'menu-item-title'    => $item[0],
					'menu-item-url'      => $item[1],
					'menu-item-type'     => 'custom',
					'menu-item-status'   => 'publish',
					'menu-item-position' => $position + 1,
				)
			);
		}
	}
	$locations = get_theme_mod( 'nav_menu_locations', array() );
	if ( empty( $locations[ $location ] ) ) {
		$locations[ $location ] = $menu_id;
		set_theme_mod( 'nav_menu_locations', $locations );
	}
	return $menu_id;
}

/**
 * Run the importer.
 *
 * @param array $options Which parts to import (keys: content, pages, menus, logo, samples).
 * @return string[] Human-readable log of what was done.
 */
function riaz_core_import_starter_content( $options ) {
	$log  = array();
	$home = home_url( '/' );

	if ( ! empty( $options['content'] ) ) {
		foreach ( riaz_core_starter_services() as $index => $service ) {
			riaz_core_insert_once(
				array(
					'post_type'    => 'riaz_service',
					'post_status'  => 'publish',
					'post_title'   => $service[1],
					'post_excerpt' => $service[2],
					'menu_order'   => $index,
					'meta_input'   => array(
						'_riaz_icon'     => $service[0],
						'_riaz_featured' => 1 === $index ? '1' : '',
					),
				)
			);
		}
		foreach ( riaz_core_starter_faqs() as $index => $faq ) {
			riaz_core_insert_once(
				array(
					'post_type'    => 'riaz_faq',
					'post_status'  => 'publish',
					'post_title'   => $faq[0],
					'post_content' => '<!-- wp:paragraph --><p>' . esc_html( $faq[1] ) . '</p><!-- /wp:paragraph -->',
					'menu_order'   => $index,
				)
			);
		}
		$log[] = __( 'Created 6 services and 5 FAQs (existing items with the same title were kept).', 'riaz-portfolio-core' );
	}

	if ( ! empty( $options['pages'] ) && current_user_can( 'manage_options' ) ) {
		$front = riaz_core_insert_once(
			array(
				'post_type'   => 'page',
				'post_status' => 'publish',
				'post_title'  => __( 'Home', 'riaz-portfolio-core' ),
			)
		);
		$blog  = riaz_core_insert_once(
			array(
				'post_type'   => 'page',
				'post_status' => 'publish',
				'post_title'  => __( 'Blog', 'riaz-portfolio-core' ),
			)
		);
		if ( $front && $blog ) {
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', $front );
			update_option( 'page_for_posts', $blog );
			$log[] = __( 'Created the Home and Blog pages and set them in Settings → Reading.', 'riaz-portfolio-core' );
		}
	}

	if ( ! empty( $options['menus'] ) ) {
		$primary = array(
			array( __( 'Home', 'riaz-portfolio-core' ), $home . '#top' ),
			array( __( 'About', 'riaz-portfolio-core' ), $home . '#about' ),
			array( __( 'Services', 'riaz-portfolio-core' ), $home . '#services' ),
			array( __( 'Approach', 'riaz-portfolio-core' ), $home . '#approach' ),
			array( __( 'Testimonials', 'riaz-portfolio-core' ), $home . '#testimonials' ),
			array( __( 'FAQ', 'riaz-portfolio-core' ), $home . '#faq' ),
		);
		riaz_core_create_menu( __( 'Primary', 'riaz-portfolio-core' ), $primary, 'primary' );
		riaz_core_create_menu(
			__( 'Footer — Explore', 'riaz-portfolio-core' ),
			array(
				array( __( 'About', 'riaz-portfolio-core' ), $home . '#about' ),
				array( __( 'Services', 'riaz-portfolio-core' ), $home . '#services' ),
				array( __( 'Approach', 'riaz-portfolio-core' ), $home . '#approach' ),
				array( __( 'FAQ', 'riaz-portfolio-core' ), $home . '#faq' ),
			),
			'footer_col1'
		);
		riaz_core_create_menu(
			__( 'Footer — Focus', 'riaz-portfolio-core' ),
			array(
				array( __( 'Digital strategy', 'riaz-portfolio-core' ), $home . '#services' ),
				array( __( 'SEO', 'riaz-portfolio-core' ), $home . '#services' ),
				array( __( 'Content', 'riaz-portfolio-core' ), $home . '#services' ),
			),
			'footer_col2'
		);
		$log[] = __( 'Created the Primary and footer menus (Appearance → Menus).', 'riaz-portfolio-core' );
	}

	if ( ! empty( $options['logo'] ) && ! get_theme_mod( 'custom_logo' ) ) {
		$logo = riaz_core_sideload_bundled_image( 'logo.png', get_bloginfo( 'name' ) );
		if ( $logo ) {
			set_theme_mod( 'custom_logo', $logo );
			$log[] = __( 'Added the bundled logo (Customize → Site Identity).', 'riaz-portfolio-core' );
		}
	}

	if ( ! empty( $options['samples'] ) ) {
		$term = term_exists( 'SEO', 'riaz_portfolio_cat' );
		if ( ! $term ) {
			$term = wp_insert_term( 'SEO', 'riaz_portfolio_cat' );
		}
		$content = term_exists( 'Content', 'riaz_portfolio_cat' );
		if ( ! $content ) {
			$content = wp_insert_term( 'Content', 'riaz_portfolio_cat' );
		}
		$samples = array(
			array( __( 'Sample project: search visibility refresh', 'riaz-portfolio-core' ), $term ),
			array( __( 'Sample project: content hub for a service brand', 'riaz-portfolio-core' ), $content ),
			array( __( 'Sample project: technical SEO clean-up', 'riaz-portfolio-core' ), $term ),
		);
		foreach ( $samples as $index => $sample ) {
			$id = riaz_core_insert_once(
				array(
					'post_type'    => 'riaz_portfolio',
					'post_status'  => 'draft',
					'post_title'   => $sample[0],
					'post_excerpt' => __( 'Replace this sample with a real project: the goal, what you did and what changed.', 'riaz-portfolio-core' ),
					'post_content' => '<!-- wp:paragraph --><p>' . esc_html__( 'Describe the challenge, your approach and the outcome. Add images, quotes and results using blocks, Elementor or Divi.', 'riaz-portfolio-core' ) . '</p><!-- /wp:paragraph -->',
					'menu_order'   => $index,
				)
			);
			if ( $id && ! is_wp_error( $sample[1] ) ) {
				wp_set_object_terms( $id, (int) $sample[1]['term_id'], 'riaz_portfolio_cat' );
			}
		}
		riaz_core_insert_once(
			array(
				'post_type'    => 'riaz_case_study',
				'post_status'  => 'draft',
				'post_title'   => __( 'Sample case study: from scattered content to a clear plan', 'riaz-portfolio-core' ),
				'post_excerpt' => __( 'Replace this sample with a real story. Only add results you can verify.', 'riaz-portfolio-core' ),
				'post_content' => '<!-- wp:heading --><h2 class="wp-block-heading">' . esc_html__( 'The challenge', 'riaz-portfolio-core' ) . '</h2><!-- /wp:heading --><!-- wp:paragraph --><p>' . esc_html__( 'What was holding growth back?', 'riaz-portfolio-core' ) . '</p><!-- /wp:paragraph --><!-- wp:heading --><h2 class="wp-block-heading">' . esc_html__( 'The approach', 'riaz-portfolio-core' ) . '</h2><!-- /wp:heading --><!-- wp:paragraph --><p>' . esc_html__( 'What did you prioritize and why?', 'riaz-portfolio-core' ) . '</p><!-- /wp:paragraph --><!-- wp:heading --><h2 class="wp-block-heading">' . esc_html__( 'The outcome', 'riaz-portfolio-core' ) . '</h2><!-- /wp:heading --><!-- wp:paragraph --><p>' . esc_html__( 'What changed for the business?', 'riaz-portfolio-core' ) . '</p><!-- /wp:paragraph -->',
			)
		);
		$plans = array(
			array( __( 'Sample plan: Audit', 'riaz-portfolio-core' ), __( 'Custom', 'riaz-portfolio-core' ), '', "Search & content audit\nPrioritized action list\nWalkthrough call", '' ),
			array( __( 'Sample plan: Growth', 'riaz-portfolio-core' ), __( 'Custom', 'riaz-portfolio-core' ), __( '/ month', 'riaz-portfolio-core' ), "Everything in Audit\nMonthly roadmap\nContent direction\nProgress reviews", '1' ),
			array( __( 'Sample plan: Partnership', 'riaz-portfolio-core' ), __( 'Custom', 'riaz-portfolio-core' ), __( '/ month', 'riaz-portfolio-core' ), "Everything in Growth\nHands-on implementation\nPriority support", '' ),
		);
		foreach ( $plans as $index => $plan ) {
			riaz_core_insert_once(
				array(
					'post_type'    => 'riaz_pricing',
					'post_status'  => 'draft',
					'post_title'   => $plan[0],
					'post_excerpt' => __( 'Replace this sample with your real offer.', 'riaz-portfolio-core' ),
					'menu_order'   => $index,
					'meta_input'   => array(
						'_riaz_price'     => $plan[1],
						'_riaz_period'    => $plan[2],
						'_riaz_features'  => $plan[3],
						'_riaz_featured'  => $plan[4],
						'_riaz_badge'     => $plan[4] ? __( 'Most popular', 'riaz-portfolio-core' ) : '',
						'_riaz_btn_label' => __( 'Start a conversation', 'riaz-portfolio-core' ),
						'_riaz_btn_url'   => '#contact',
					),
				)
			);
		}
		$log[] = __( 'Created sample projects, a case study and pricing plans as drafts. Edit and publish them when they contain real information.', 'riaz-portfolio-core' );
	}

	update_option( 'riaz_starter_imported', time(), false );

	/**
	 * Fires after the starter content importer ran.
	 *
	 * @param array $options Options used.
	 * @param array $log     Log messages.
	 */
	do_action( 'riaz_core_starter_content_imported', $options, $log );

	return $log;
}

/**
 * Handle the import form.
 */
function riaz_core_handle_import() {
	if ( ! current_user_can( 'edit_theme_options' ) || ! current_user_can( 'publish_posts' ) ) {
		wp_die( esc_html__( 'You are not allowed to import content.', 'riaz-portfolio-core' ), 403 );
	}
	check_admin_referer( 'riaz_import_starter', 'riaz_import_nonce' );

	$parts   = array( 'content', 'pages', 'menus', 'logo', 'samples' );
	$options = array();
	foreach ( $parts as $part ) {
		$options[ $part ] = ! empty( $_POST[ 'riaz_import_' . $part ] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- verified above.
	}

	$log = riaz_core_import_starter_content( $options );
	set_transient( 'riaz_import_log_' . get_current_user_id(), $log, MINUTE_IN_SECONDS * 5 );

	$back = wp_get_referer() ? wp_get_referer() : admin_url( 'themes.php?page=riaz-setup' );
	wp_safe_redirect( add_query_arg( 'imported', 1, $back ) );
	exit;
}
add_action( 'admin_post_riaz_import_starter', 'riaz_core_handle_import' );

/**
 * Import form, embedded on the theme's setup page.
 */
function riaz_core_render_import_form() {
	?>
	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="riaz_import_starter">
					<?php wp_nonce_field( 'riaz_import_starter', 'riaz_import_nonce' ); ?>
					<fieldset>
						<legend class="screen-reader-text"><?php esc_html_e( 'What to import', 'riaz-portfolio-core' ); ?></legend>
						<label><input type="checkbox" name="riaz_import_content" value="1" checked> <?php esc_html_e( 'Services (6) and FAQs (5) from the design', 'riaz-portfolio-core' ); ?></label>
						<?php if ( current_user_can( 'manage_options' ) ) : ?>
							<label><input type="checkbox" name="riaz_import_pages" value="1" checked> <?php esc_html_e( 'Home and Blog pages, set as front page and posts page', 'riaz-portfolio-core' ); ?></label>
						<?php endif; ?>
						<label><input type="checkbox" name="riaz_import_menus" value="1" checked> <?php esc_html_e( 'Primary and footer menus', 'riaz-portfolio-core' ); ?></label>
						<label><input type="checkbox" name="riaz_import_logo" value="1" checked> <?php esc_html_e( 'Bundled logo (only if no logo is set)', 'riaz-portfolio-core' ); ?></label>
						<label><input type="checkbox" name="riaz_import_samples" value="1"> <?php esc_html_e( 'Sample projects, case study and pricing plans (saved as drafts)', 'riaz-portfolio-core' ); ?></label>
					</fieldset>
					<?php submit_button( __( 'Import starter content', 'riaz-portfolio-core' ), 'primary', 'submit', false ); ?>
				</form>
	<?php
}
