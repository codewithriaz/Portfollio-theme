<?php
/**
 * Divi Builder (plugin) integration.
 *
 * The Divi Builder plugin works with any theme that outputs the_content().
 * This file teaches the theme to recognise Divi 4 and Divi 5 layouts so they
 * are shown full width. (Enabling the builder on the portfolio content types
 * is handled by the Riaz Portfolio Core plugin.)
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * Detect Divi 5 content (stored as divi/* blocks) as builder content.
 *
 * @param bool $is_builder Detected state.
 * @param int  $post_id    Post ID.
 * @return bool
 */
function riaz_divi_detect( $is_builder, $post_id ) {
	if ( $is_builder ) {
		return true;
	}
	$content = (string) get_post_field( 'post_content', $post_id );
	return false !== strpos( $content, '<!-- wp:divi/' ) || false !== strpos( $content, '[et_pb_section' );
}
add_filter( 'riaz_is_builder_content', 'riaz_divi_detect', 10, 2 );
