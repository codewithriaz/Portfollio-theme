<?php
/**
 * Small, reusable helpers used across templates.
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * Get a theme option, falling back to the default declared in the Customizer schema.
 *
 * @param string $key Setting key.
 * @return mixed
 */
function riaz_mod( $key ) {
	return get_theme_mod( $key, riaz_default( $key ) );
}

/**
 * Get a boolean theme option.
 *
 * @param string $key Setting key.
 * @return bool
 */
function riaz_mod_bool( $key ) {
	return (bool) riaz_mod( $key );
}

/**
 * Decode a repeater option into a list of rows.
 *
 * @param string $key Setting key.
 * @return array[]
 */
function riaz_mod_list( $key ) {
	$value = riaz_mod( $key );
	if ( is_string( $value ) ) {
		$value = json_decode( $value, true );
	}
	if ( ! is_array( $value ) ) {
		return array();
	}
	return array_values( array_filter( $value, 'is_array' ) );
}

/**
 * URL of a bundled theme asset (child themes can override the file).
 *
 * @param string $path Path relative to /assets.
 * @return string
 */
function riaz_asset( $path ) {
	return get_theme_file_uri( 'assets/' . ltrim( $path, '/' ) );
}

/**
 * Allowed HTML for short inline strings such as titles and labels.
 *
 * @return array
 */
function riaz_inline_kses() {
	return array(
		'br'     => array(),
		'em'     => array(),
		'strong' => array(),
		'span'   => array( 'class' => true ),
		'a'      => array(
			'href'   => true,
			'target' => true,
			'rel'    => true,
		),
	);
}

/**
 * Echo a short inline string with limited HTML.
 *
 * @param string $text Text.
 */
function riaz_inline( $text ) {
	echo wp_kses( (string) $text, riaz_inline_kses() );
}

/**
 * Format a multi-paragraph text option.
 *
 * @param string $text Text.
 * @return string
 */
function riaz_paragraphs( $text ) {
	return wpautop( wp_kses_post( (string) $text ) );
}

/**
 * Whether the current request renders the homepage sections.
 *
 * @return bool
 */
function riaz_is_sections_page() {
	if ( is_front_page() && riaz_front_page_uses_sections() ) {
		return true;
	}
	return is_page_template( 'templates/template-homepage.php' );
}

/**
 * Make in-page anchors work from any page: "#about" becomes "https://site/#about"
 * outside the homepage. Other URLs are returned untouched.
 *
 * @param string $url Raw URL.
 * @return string
 */
function riaz_link_url( $url ) {
	$url = trim( (string) $url );
	if ( '' === $url ) {
		return '';
	}
	if ( 0 === strpos( $url, '#' ) && strlen( $url ) > 1 && ! riaz_is_sections_page() ) {
		return home_url( '/' ) . $url;
	}
	return $url;
}

/**
 * Link attributes for external URLs (new tab + safe rel).
 *
 * @param string $url URL.
 * @return string Attribute string, already escaped.
 */
function riaz_link_target( $url ) {
	$host = wp_parse_url( $url, PHP_URL_HOST );
	if ( $host && wp_parse_url( home_url(), PHP_URL_HOST ) !== $host ) {
		return ' target="_blank" rel="noopener noreferrer"';
	}
	return '';
}

/**
 * Render a pill button.
 *
 * @param string $label Button label.
 * @param string $url   Destination.
 * @param string $style 'primary', 'light', 'ghost' or 'outline'.
 * @param array  $args  Optional: 'icon' (icon name or false), 'class'.
 */
function riaz_button( $label, $url, $style = 'primary', $args = array() ) {
	if ( '' === trim( (string) $label ) || '' === trim( (string) $url ) ) {
		return;
	}
	$args    = wp_parse_args(
		$args,
		array(
			'icon'  => 'arrow-right',
			'class' => '',
		)
	);
	$classes = array(
		'primary' => 'btn',
		'light'   => 'btn btn-light',
		'ghost'   => 'btn-ghost',
		'outline' => 'btn-outline',
	);
	$class   = isset( $classes[ $style ] ) ? $classes[ $style ] : 'btn';
	$href    = riaz_link_url( $url );
	$has_pip = $args['icon'] && in_array( $style, array( 'primary', 'light' ), true );

	printf(
		'<a class="%1$s" href="%2$s"%3$s>%4$s%5$s</a>',
		esc_attr( trim( $class . ' ' . $args['class'] ) ),
		esc_url( $href ),
		riaz_link_target( $href ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static attribute string.
		esc_html( $label ),
		$has_pip ? '<span class="btn-icon" aria-hidden="true">' . riaz_get_icon( $args['icon'], 16 ) . '</span>' : '' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted SVG.
	);
}

/**
 * Render a text link with a trailing arrow.
 *
 * @param string $label Link label.
 * @param string $url   Destination.
 * @param string $icon  Icon name.
 */
function riaz_link_arrow( $label, $url, $icon = 'arrow-up-right' ) {
	if ( '' === trim( (string) $label ) || '' === trim( (string) $url ) ) {
		return;
	}
	$href = riaz_link_url( $url );
	printf(
		'<a class="link-arrow" href="%1$s"%2$s>%3$s %4$s</a>',
		esc_url( $href ),
		riaz_link_target( $href ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static attribute string.
		esc_html( $label ),
		riaz_get_icon( $icon, 16 ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted SVG.
	);
}

/**
 * Render a section heading block (eyebrow, title with highlight, description).
 *
 * @param array $args {
 *     Heading options.
 *
 *     @type string $eyebrow     Small label above the title.
 *     @type string $title       Title text.
 *     @type string $highlight   Accent-colored continuation of the title.
 *     @type string $description Supporting paragraph.
 *     @type bool   $centered    Center align.
 *     @type bool   $dark        Light text for dark backgrounds.
 *     @type string $tag         Heading tag, h1 or h2.
 * }
 */
function riaz_section_heading( $args ) {
	$args = wp_parse_args(
		$args,
		array(
			'eyebrow'     => '',
			'title'       => '',
			'highlight'   => '',
			'description' => '',
			'centered'    => false,
			'dark'        => false,
			'tag'         => 'h2',
		)
	);
	if ( '' === $args['title'] && '' === $args['highlight'] && '' === $args['eyebrow'] ) {
		return;
	}
	$classes = array( 'section-heading' );
	if ( $args['centered'] ) {
		$classes[] = 'section-heading-centered';
	}
	if ( $args['dark'] ) {
		$classes[] = 'section-heading-dark';
	}
	$tag = in_array( $args['tag'], array( 'h1', 'h2', 'h3' ), true ) ? $args['tag'] : 'h2';
	?>
	<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
		<?php if ( $args['eyebrow'] ) : ?>
			<p class="eyebrow"><?php riaz_icon( 'sparkles', 13 ); ?> <?php echo esc_html( $args['eyebrow'] ); ?></p>
		<?php endif; ?>
		<?php if ( $args['title'] || $args['highlight'] ) : ?>
			<<?php echo esc_html( $tag ); ?>>
				<?php riaz_inline( $args['title'] ); ?>
				<?php if ( $args['highlight'] ) : ?>
					<span class="text-accent"><?php riaz_inline( $args['highlight'] ); ?></span>
				<?php endif; ?>
			</<?php echo esc_html( $tag ); ?>>
		<?php endif; ?>
		<?php if ( $args['description'] ) : ?>
			<div class="section-description"><?php echo wp_kses_post( $args['description'] ); ?></div>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Heading arguments for a homepage section read from its Customizer prefix.
 *
 * @param string $prefix Option prefix, e.g. 'services'.
 * @param array  $extra  Extra arguments merged in.
 * @return array
 */
function riaz_heading_args( $prefix, $extra = array() ) {
	return array_merge(
		array(
			'eyebrow'     => riaz_mod( $prefix . '_eyebrow' ),
			'title'       => riaz_mod( $prefix . '_title' ),
			'highlight'   => riaz_mod( $prefix . '_highlight' ),
			'description' => riaz_mod( $prefix . '_text' ),
		),
		$extra
	);
}

/**
 * Image markup for a Customizer image option with a bundled fallback.
 *
 * @param string $key      Setting key holding an attachment ID.
 * @param array  $fallback Bundled image: 'src', 'srcset' (optional), 'width', 'height'.
 * @param array  $attr     Image attributes (alt, sizes, loading, class, fetchpriority).
 * @return string
 */
function riaz_get_option_image( $key, $fallback, $attr = array() ) {
	$attr = wp_parse_args(
		$attr,
		array(
			'alt'      => '',
			'sizes'    => '100vw',
			'loading'  => 'lazy',
			'decoding' => 'async',
		)
	);

	$id = absint( riaz_mod( $key ) );
	if ( $id && wp_attachment_is_image( $id ) ) {
		$custom_alt = get_post_meta( $id, '_wp_attachment_image_alt', true );
		if ( $custom_alt ) {
			$attr['alt'] = $custom_alt;
		}
		return wp_get_attachment_image( $id, 'full', false, $attr );
	}

	if ( empty( $fallback['src'] ) ) {
		return '';
	}

	$html = sprintf(
		'<img src="%1$s"%2$s width="%3$d" height="%4$d" alt="%5$s"',
		esc_url( $fallback['src'] ),
		! empty( $fallback['srcset'] ) ? ' srcset="' . esc_attr( $fallback['srcset'] ) . '" sizes="' . esc_attr( $attr['sizes'] ) . '"' : '',
		absint( $fallback['width'] ),
		absint( $fallback['height'] ),
		esc_attr( $attr['alt'] )
	);
	foreach ( array( 'class', 'loading', 'decoding', 'fetchpriority' ) as $name ) {
		if ( ! empty( $attr[ $name ] ) ) {
			$html .= sprintf( ' %s="%s"', esc_attr( $name ), esc_attr( $attr[ $name ] ) );
		}
	}
	return $html . '>';
}

/**
 * Bundled default images used until the site owner uploads their own.
 *
 * @param string $name 'portrait' or 'about'.
 * @return array
 */
function riaz_bundled_image( $name ) {
	$images = array(
		'portrait' => array(
			'src'    => riaz_asset( 'images/hero-portrait.webp' ),
			'srcset' => riaz_asset( 'images/hero-portrait-480.webp' ) . ' 480w, ' . riaz_asset( 'images/hero-portrait.webp' ) . ' 787w',
			'width'  => 787,
			'height' => 768,
		),
		'about'    => array(
			'src'    => riaz_asset( 'images/about-portrait.webp' ),
			'srcset' => riaz_asset( 'images/about-portrait-480.webp' ) . ' 480w, ' . riaz_asset( 'images/about-portrait.webp' ) . ' 880w',
			'width'  => 880,
			'height' => 1100,
		),
	);
	/**
	 * Filter the bundled fallback images.
	 *
	 * @param array $images Image definitions keyed by name.
	 */
	$images = apply_filters( 'riaz_bundled_images', $images );
	return isset( $images[ $name ] ) ? $images[ $name ] : array();
}

/**
 * Configured social profiles.
 *
 * @return array<string,array{url:string,label:string}>
 */
function riaz_get_social_links() {
	$links = array();
	foreach ( riaz_social_networks() as $network => $label ) {
		$url = riaz_mod( 'social_' . $network );
		if ( $url ) {
			$links[ $network ] = array(
				'url'   => $url,
				'label' => $label,
			);
		}
	}
	return $links;
}

/**
 * Supported social networks.
 *
 * @return array<string,string>
 */
function riaz_social_networks() {
	return array(
		'linkedin'  => 'LinkedIn',
		'x'         => 'X (Twitter)',
		'facebook'  => 'Facebook',
		'instagram' => 'Instagram',
		'youtube'   => 'YouTube',
		'tiktok'    => 'TikTok',
		'github'    => 'GitHub',
		'dribbble'  => 'Dribbble',
		'behance'   => 'Behance',
		'whatsapp'  => 'WhatsApp',
	);
}

/**
 * Render the social icon list.
 *
 * @param string $extra_class Extra class for the list.
 */
function riaz_social_list( $extra_class = '' ) {
	$links = riaz_get_social_links();
	if ( ! $links ) {
		return;
	}
	echo '<ul class="social-links ' . esc_attr( $extra_class ) . '">';
	foreach ( $links as $network => $link ) {
		printf(
			'<li><a href="%1$s" target="_blank" rel="noopener noreferrer me">%2$s<span class="screen-reader-text">%3$s</span></a></li>',
			esc_url( $link['url'] ),
			riaz_get_icon( $network, 16 ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted SVG.
			/* translators: %s: social network name. */
			esc_html( sprintf( __( '%s (opens in a new tab)', 'riaz-portfolio' ), $link['label'] ) )
		);
	}
	echo '</ul>';
}

/**
 * Read a post meta value with a default.
 *
 * @param int    $post_id Post ID.
 * @param string $key     Meta key without the leading "_riaz_".
 * @param mixed  $fallback Default value.
 * @return mixed
 */
function riaz_meta( $post_id, $key, $fallback = '' ) {
	$value = get_post_meta( $post_id, '_riaz_' . $key, true );
	return ( '' === $value || null === $value ) ? $fallback : $value;
}

/**
 * Extract an 11-character YouTube video ID from a URL or raw ID.
 *
 * @param string $value URL or ID.
 * @return string Empty string when no ID is found.
 */
function riaz_youtube_id( $value ) {
	$value = trim( (string) $value );
	if ( preg_match( '/^[A-Za-z0-9_-]{11}$/', $value ) ) {
		return $value;
	}
	if ( preg_match( '~(?:youtu\.be/|youtube(?:-nocookie)?\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/|live/|v/))([A-Za-z0-9_-]{11})~', $value, $matches ) ) {
		return $matches[1];
	}
	return '';
}

/**
 * Initials for avatar fallbacks.
 *
 * @param string $name Full name.
 * @return string
 */
function riaz_initials( $name ) {
	$words    = preg_split( '/\s+/', trim( wp_strip_all_tags( $name ) ) );
	$initials = '';
	foreach ( array_slice( (array) $words, 0, 2 ) as $word ) {
		$initials .= function_exists( 'mb_substr' ) ? mb_substr( $word, 0, 1 ) : substr( $word, 0, 1 );
	}
	return function_exists( 'mb_strtoupper' ) ? mb_strtoupper( $initials ) : strtoupper( $initials );
}

/**
 * Estimated reading time in minutes.
 *
 * @param int|WP_Post|null $post Post.
 * @return int
 */
function riaz_reading_time( $post = null ) {
	$content = get_post_field( 'post_content', $post );
	$words   = str_word_count( wp_strip_all_tags( strip_shortcodes( (string) $content ) ) );
	return max( 1, (int) ceil( $words / 220 ) );
}

/**
 * Whether a post's content is managed by a page builder.
 *
 * @param int $post_id Post ID.
 * @return bool
 */
function riaz_is_builder_content( $post_id ) {
	if ( ! $post_id ) {
		return false;
	}
	if ( 'builder' === get_post_meta( $post_id, '_elementor_edit_mode', true ) ) {
		return true;
	}
	if ( 'on' === get_post_meta( $post_id, '_et_pb_use_builder', true ) ) {
		return true;
	}
	/**
	 * Filter whether a post is built with a page builder.
	 *
	 * @param bool $is_builder Detected state.
	 * @param int  $post_id    Post ID.
	 */
	return (bool) apply_filters( 'riaz_is_builder_content', false, $post_id );
}

/**
 * Whether the static front page should render the theme's homepage sections.
 *
 * Sections are used when "Your latest posts" is selected (unless disabled), or
 * when the chosen front page is empty. A front page with block, Elementor or
 * Divi content renders that content instead.
 *
 * @return bool
 */
function riaz_front_page_uses_sections() {
	if ( 'page' !== get_option( 'show_on_front' ) ) {
		return riaz_mod_bool( 'home_use_sections' );
	}
	$page_id = (int) get_option( 'page_on_front' );
	if ( ! $page_id ) {
		return true;
	}
	if ( riaz_is_builder_content( $page_id ) ) {
		return false;
	}
	return '' === trim( (string) get_post_field( 'post_content', $page_id ) );
}

/**
 * Render an admin-only hint where an empty section would appear.
 *
 * @param string $title   Short heading.
 * @param string $message Explanation.
 * @param string $url     Admin URL to fix it.
 * @param string $label   Link label.
 */
function riaz_admin_placeholder( $title, $message, $url, $label ) {
	// Only inside the Customizer preview, so the live site never shows hints.
	if ( ! is_customize_preview() || ! current_user_can( 'edit_posts' ) ) {
		return;
	}
	?>
	<div class="section-shell">
		<div class="rp-placeholder" role="note">
			<div>
				<strong><?php echo esc_html( $title ); ?></strong>
				<?php echo esc_html( $message ); ?>
				<em>(<?php esc_html_e( 'Only visible while customizing.', 'riaz-portfolio' ); ?>)</em>
			</div>
			<a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?> &rarr;</a>
		</div>
	</div>
	<?php
}

/**
 * Destination of a service card's "Learn more" link.
 *
 * Custom link → the service page (when it has content) → the default card link.
 *
 * @param int $post_id Service ID.
 * @return string
 */
function riaz_service_link( $post_id ) {
	$custom = riaz_meta( $post_id, 'link_url' );
	if ( $custom ) {
		return riaz_link_url( $custom );
	}
	if ( '' !== trim( (string) get_post_field( 'post_content', $post_id ) ) ) {
		return get_permalink( $post_id );
	}
	return riaz_link_url( riaz_mod( 'services_card_url' ) );
}

/**
 * Result metrics of a case study.
 *
 * @param int $post_id Case study ID.
 * @param int $limit   Maximum metrics.
 * @return array[] List of { value, label }.
 */
function riaz_case_metrics( $post_id, $limit = 3 ) {
	$metrics = array();
	for ( $i = 1; $i <= 3; $i++ ) {
		$value = riaz_meta( $post_id, 'metric' . $i . '_value' );
		if ( '' !== $value ) {
			$metrics[] = array(
				'value' => $value,
				'label' => riaz_meta( $post_id, 'metric' . $i . '_label' ),
			);
		}
	}
	return array_slice( $metrics, 0, $limit );
}
