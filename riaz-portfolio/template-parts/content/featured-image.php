<?php
/**
 * Large featured image that overlaps the page banner.
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;

if ( ! has_post_thumbnail() || post_password_required() ) {
	return;
}
?>
<figure class="entry-featured<?php echo riaz_show_page_banner() ? '' : ' is-flat'; ?>">
	<?php
	the_post_thumbnail(
		'riaz-wide',
		array(
			'loading'       => 'eager',
			'fetchpriority' => 'high',
			'sizes'         => '(max-width: 1340px) 100vw, 1280px',
		)
	);
	?>
</figure>
