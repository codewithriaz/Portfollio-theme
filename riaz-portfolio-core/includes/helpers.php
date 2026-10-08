<?php
/**
 * Plugin helpers.
 *
 * @package Riaz_Portfolio_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Read a post meta value stored with the shared "_riaz_" prefix.
 *
 * @param int    $post_id Post ID.
 * @param string $key     Key without prefix.
 * @param mixed  $fallback Default.
 * @return mixed
 */
function riaz_core_meta( $post_id, $key, $fallback = '' ) {
	$value = get_post_meta( $post_id, '_riaz_' . $key, true );
	return ( '' === $value || null === $value ) ? $fallback : $value;
}

/**
 * Extract a YouTube video ID from a URL or ID.
 *
 * @param string $value URL or ID.
 * @return string
 */
function riaz_core_youtube_id( $value ) {
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
 * Whether the Riaz Portfolio theme (or a child theme of it) is active.
 *
 * @return bool
 */
function riaz_core_theme_active() {
	return function_exists( 'riaz_get_section_html' );
}

/**
 * Icon names for service and step pickers (mirrors the theme's icon set).
 *
 * @return array<string,string>
 */
function riaz_core_icon_choices() {
	if ( function_exists( 'riaz_icon_choices' ) ) {
		return riaz_icon_choices();
	}
	$names   = array( 'search', 'target', 'settings-2', 'pen-line', 'trending-up', 'chart-line', 'chart-pie', 'chart-column', 'file-text', 'compass', 'sparkles', 'lightbulb', 'mail', 'phone', 'map-pin', 'clock', 'calendar', 'quote', 'star', 'briefcase', 'users', 'user', 'layers', 'award', 'rocket', 'globe', 'megaphone', 'code', 'palette', 'camera', 'shopping-bag', 'heart', 'zap', 'shield', 'message-circle', 'send', 'external-link', 'folder', 'tag', 'monitor', 'smartphone', 'book-open', 'building-2', 'store', 'handshake', 'gauge', 'circle-check', 'mouse-pointer-click', 'video', 'image', 'link', 'layout-grid', 'check', 'play', 'arrow-right', 'arrow-up-right' );
	$choices = array();
	foreach ( $names as $name ) {
		$choices[ $name ] = ucwords( str_replace( '-', ' ', $name ) );
	}
	return $choices;
}
