<?php
/**
 * Front-end assets, fonts and the dynamic CSS generated from theme options.
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * Cache-busting version for a theme file (file time during development).
 *
 * @param string $relative Path relative to the theme root.
 * @return string
 */
function riaz_asset_version( $relative ) {
	if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
		$path = get_theme_file_path( $relative );
		if ( file_exists( $path ) ) {
			return (string) filemtime( $path );
		}
	}
	return RIAZ_VERSION;
}

/**
 * Path of an asset, preferring the minified build unless SCRIPT_DEBUG is on.
 *
 * @param string $path Path relative to /assets, e.g. css/main.css.
 * @return string
 */
function riaz_asset_min( $path ) {
	if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
		return $path;
	}
	$min = preg_replace( '/\.(css|js)$/', '.min.$1', $path );
	return file_exists( get_theme_file_path( 'assets/' . $min ) ) ? $min : $path;
}

/**
 * Enqueue styles and scripts.
 */
function riaz_enqueue_assets() {
	$google = riaz_google_fonts_url();
	if ( $google ) {
		wp_enqueue_style( 'riaz-fonts', $google, array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- Google Fonts URL.
	}

	$css = riaz_asset_min( 'css/main.css' );
	$js  = riaz_asset_min( 'js/main.js' );

	wp_enqueue_style( 'riaz-main', riaz_asset( $css ), array(), riaz_asset_version( 'assets/' . $css ) );
	wp_add_inline_style( 'riaz-main', riaz_dynamic_css() );

	wp_enqueue_script(
		'riaz-main',
		riaz_asset( $js ),
		array(),
		riaz_asset_version( 'assets/' . $js ),
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'riaz_enqueue_assets' );

/**
 * Tiny head script: flags JS support and prepares scroll reveals before first
 * paint. If main.js never runs, the reveal flag is removed so content shows.
 */
function riaz_head_bootstrap() {
	$reveal = riaz_mod_bool( 'anim_enable' ) ? 'true' : 'false';
	$script = "(function(d){d.classList.remove('no-js');d.classList.add('js');if(!{$reveal}||!('IntersectionObserver' in window)||matchMedia('(prefers-reduced-motion: reduce)').matches)return;d.classList.add('reveal-ready');setTimeout(function(){if(!window.riazReady)d.classList.remove('reveal-ready');},3000);})(document.documentElement);";
	wp_print_inline_script_tag( $script );
}
add_action( 'wp_head', 'riaz_head_bootstrap', 1 );

/**
 * Preload the font and (on the homepage) the hero portrait.
 */
function riaz_preload_assets() {
	if ( 'roboto' === riaz_mod( 'font_body' ) && riaz_mod_bool( 'perf_preload_font' ) ) {
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( riaz_asset( 'fonts/roboto-latin.woff2' ) )
		);
	}

	if ( riaz_google_fonts_url() ) {
		echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
		echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
	}

	$sections = riaz_is_sections_page() ? riaz_get_enabled_sections() : array();
	if ( $sections && 'hero' === $sections[0] ) {
		$id = absint( riaz_mod( 'hero_image' ) );
		if ( $id && wp_attachment_is_image( $id ) ) {
			$src    = wp_get_attachment_image_url( $id, 'full' );
			$srcset = wp_get_attachment_image_srcset( $id, 'full' );
		} else {
			$fallback = riaz_bundled_image( 'portrait' );
			$src      = $fallback['src'];
			$srcset   = $fallback['srcset'];
		}
		if ( $src ) {
			printf(
				'<link rel="preload" as="image" href="%1$s"%2$s imagesizes="(max-width: 900px) 90vw, 46vw" fetchpriority="high">' . "\n",
				esc_url( $src ),
				$srcset ? ' imagesrcset="' . esc_attr( $srcset ) . '"' : ''
			);
		}
	}
}
add_action( 'wp_head', 'riaz_preload_assets', 2 );

/**
 * Google Fonts stylesheet URL for the selected fonts, or empty string.
 *
 * @return string
 */
function riaz_google_fonts_url() {
	$fonts    = riaz_google_fonts();
	$families = array();
	foreach ( array( riaz_mod( 'font_body' ), riaz_mod( 'font_heading' ) ) as $slug ) {
		if ( isset( $fonts[ $slug ] ) ) {
			$families[ $slug ] = 'family=' . str_replace( ' ', '+', $fonts[ $slug ]['name'] ) . ':' . $fonts[ $slug ]['spec'];
		}
	}
	if ( ! $families ) {
		return '';
	}
	return 'https://fonts.googleapis.com/css2?' . implode( '&', $families ) . '&display=swap';
}

/**
 * CSS font-family stack for a font option.
 *
 * @param string $slug Font slug.
 * @return string
 */
function riaz_font_stack( $slug ) {
	$system = 'system-ui, -apple-system, "Segoe UI", Helvetica, Arial, sans-serif';
	if ( 'roboto' === $slug ) {
		return '"Roboto", ' . $system;
	}
	$fonts = riaz_google_fonts();
	if ( isset( $fonts[ $slug ] ) ) {
		return '"' . $fonts[ $slug ]['name'] . '", ' . ( 'serif' === $fonts[ $slug ]['fallback'] ? 'Georgia, serif' : $system );
	}
	return $system;
}

/**
 * CSS custom properties generated from the Customizer.
 *
 * @return string
 */
function riaz_dynamic_css() {
	$vars = array();

	foreach ( riaz_customizer_fields() as $key => $field ) {
		if ( 'color' === $field['type'] && ! empty( $field['css_var'] ) ) {
			$color = sanitize_hex_color( riaz_mod( $key ) );
			if ( $color && strtolower( $color ) !== strtolower( $field['default'] ) ) {
				$vars[ $field['css_var'] ] = $color;
			}
		}
	}

	$body = riaz_mod( 'font_body' );
	if ( 'roboto' !== $body ) {
		$vars['--rp-font-body'] = riaz_font_stack( $body );
	}
	$heading = riaz_mod( 'font_heading' );
	if ( 'inherit' !== $heading ) {
		$vars['--rp-font-heading'] = riaz_font_stack( $heading );
	}

	$size = absint( riaz_mod( 'font_size' ) );
	if ( $size && 16 !== $size ) {
		$vars['--rp-font-size'] = $size . 'px';
	}

	$weight = absint( riaz_mod( 'heading_weight' ) );
	if ( $weight && 700 !== $weight ) {
		$vars['--rp-heading-weight'] = (string) $weight;
	}

	$width = absint( riaz_mod( 'container_width' ) );
	if ( $width && 1280 !== $width ) {
		$vars['--rp-container'] = $width . 'px';
	}

	$spacing      = array(
		'compact'  => '88px',
		'spacious' => '150px',
	);
	$space        = riaz_mod( 'section_spacing' );
	$desktop_only = isset( $spacing[ $space ] ) ? '@media (min-width:601px){:root{--rp-section-space:' . $spacing[ $space ] . ';}}' : '';

	$logo = absint( riaz_mod( 'logo_width' ) );
	if ( $logo ) {
		$vars['--rp-logo-width'] = $logo . 'px';
	}

	$css = '';
	foreach ( $vars as $name => $value ) {
		$css .= $name . ':' . $value . ';';
	}

	return ( $css ? ':root{' . $css . '}' : '' ) . $desktop_only;
}
