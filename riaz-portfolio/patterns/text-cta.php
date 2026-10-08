<?php
/**
 * Title: Intro text with buttons
 * Slug: riaz-portfolio/text-cta
 * Categories: riaz-portfolio, text
 * Keywords: intro, heading, buttons
 * Description: A heading, paragraph and two buttons built from core blocks (fully editable inline).
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"align":"wide","layout":{"type":"constrained","contentSize":"760px"}} -->
<div class="wp-block-group alignwide">
<!-- wp:heading {"level":2,"fontSize":"xx-large"} -->
<h2 class="wp-block-heading has-xx-large-font-size"><?php esc_html_e( 'Clear strategy. Steady growth.', 'riaz-portfolio' ); ?></h2>
<!-- /wp:heading -->
<!-- wp:paragraph {"fontSize":"large","textColor":"ink-soft"} -->
<p class="has-ink-soft-color has-text-color has-large-font-size"><?php esc_html_e( 'Write a short introduction here. Explain who you help, what you do for them and what they can expect from working with you.', 'riaz-portfolio' ); ?></p>
<!-- /wp:paragraph -->
<!-- wp:buttons -->
<div class="wp-block-buttons">
<!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#contact"><?php esc_html_e( 'Start a conversation', 'riaz-portfolio' ); ?></a></div>
<!-- /wp:button -->
<!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="#services"><?php esc_html_e( 'View services', 'riaz-portfolio' ); ?></a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->
</div>
<!-- /wp:group -->
