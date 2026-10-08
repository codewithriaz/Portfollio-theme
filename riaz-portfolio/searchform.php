<?php
/**
 * Search form.
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;

$riaz_search_id = wp_unique_id( 'search-' );
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $riaz_search_id ); ?>"><?php esc_html_e( 'Search for:', 'riaz-portfolio' ); ?></label>
	<input type="search" id="<?php echo esc_attr( $riaz_search_id ); ?>" class="search-field" placeholder="<?php esc_attr_e( 'Search…', 'riaz-portfolio' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s">
	<button type="submit" class="search-submit"><?php riaz_icon( 'search', 18 ); ?><span class="screen-reader-text"><?php esc_html_e( 'Search', 'riaz-portfolio' ); ?></span></button>
</form>
