<?php
/**
 * Divi Builder: enable the builder on projects, case studies and services.
 *
 * @package Riaz_Portfolio_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Add the content types to Divi's builder post types.
 *
 * @param string[] $post_types Post types.
 * @return string[]
 */
function riaz_core_divi_post_types( $post_types ) {
	return array_values( array_unique( array_merge( (array) $post_types, array( 'riaz_portfolio', 'riaz_case_study', 'riaz_service' ) ) ) );
}
add_filter( 'et_builder_post_types', 'riaz_core_divi_post_types' );
add_filter( 'et_builder_third_party_post_types', 'riaz_core_divi_post_types' );
