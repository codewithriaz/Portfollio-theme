<?php
/**
 * Server render for the Theme Section block.
 *
 * @package Riaz_Portfolio_Core
 *
 * @var array $attributes Block attributes.
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'riaz_get_section_html' ) ) {
	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		echo '<p>' . esc_html__( 'Theme sections need the Riaz Portfolio theme.', 'riaz-portfolio-core' ) . '</p>';
	}
	return;
}

$riaz_slug = isset( $attributes['section'] ) ? sanitize_key( $attributes['section'] ) : '';
$riaz_args = array( 'context' => 'block' );
if ( ! empty( $attributes['anchorId'] ) ) {
	$riaz_args['id'] = sanitize_html_class( $attributes['anchorId'] );
}

$riaz_html = riaz_get_section_html( $riaz_slug, $riaz_args );

if ( '' === trim( $riaz_html ) && defined( 'REST_REQUEST' ) && REST_REQUEST ) {
	$riaz_html = '<div class="rp-placeholder">' . esc_html__( 'This section has no content yet. Add items in the WordPress admin and it will appear here.', 'riaz-portfolio-core' ) . '</div>';
}

echo $riaz_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- section templates escape their output.
