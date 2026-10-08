<?php
/**
 * Blog sidebar.
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}
?>
<aside id="secondary" class="widget-area" aria-label="<?php esc_attr_e( 'Sidebar', 'riaz-portfolio' ); ?>">
	<?php dynamic_sidebar( 'sidebar-1' ); ?>
</aside>
