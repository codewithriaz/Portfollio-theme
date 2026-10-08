<?php
/**
 * Main template: the blog index and the fallback for every other view.
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;

get_header();
get_template_part( 'template-parts/content/archive' );
get_footer();
