<?php
/**
 * Card: Blog post or any search result (used in the loop).
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;

$riaz_type  = get_post_type();
$riaz_label = '';
if ( 'post' === $riaz_type ) {
	$riaz_cats  = get_the_category();
	$riaz_label = $riaz_cats ? $riaz_cats[0]->name : '';
} else {
	$riaz_obj   = get_post_type_object( $riaz_type );
	$riaz_label = $riaz_obj ? $riaz_obj->labels->singular_name : '';
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card' ); ?> data-reveal-item>
	<?php if ( is_sticky() && is_home() && ! is_paged() ) : ?>
		<span class="sticky-badge"><?php esc_html_e( 'Featured', 'riaz-portfolio' ); ?></span>
	<?php endif; ?>
	<?php riaz_card_media( 'riaz-card' ); ?>
	<div class="post-card-body">
		<?php if ( $riaz_label ) : ?>
			<span class="chip"><?php echo esc_html( $riaz_label ); ?></span>
		<?php endif; ?>
		<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<p><?php echo esc_html( wp_strip_all_tags( get_the_excerpt() ) ); ?></p>
		<?php if ( 'post' === $riaz_type ) : ?>
			<?php riaz_post_meta(); ?>
		<?php endif; ?>
	</div>
</article>
