<?php
/**
 * Related posts / more projects below a single entry.
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;

$riaz_type = get_post_type();
$riaz_args = array(
	'post_type'           => $riaz_type,
	'post__not_in'        => array( get_the_ID() ),
	'posts_per_page'      => 3,
	'ignore_sticky_posts' => true,
	'no_found_rows'       => true,
);

if ( 'post' === $riaz_type ) {
	if ( ! riaz_mod_bool( 'blog_related' ) ) {
		return;
	}
	$riaz_args['category__in'] = wp_get_post_categories( get_the_ID() );
	$riaz_title                = __( 'Keep reading', 'riaz-portfolio' );
	$riaz_card                 = 'post';
	$riaz_grid                 = 'posts-grid';
} elseif ( 'riaz_portfolio' === $riaz_type ) {
	$riaz_title = __( 'More projects', 'riaz-portfolio' );
	$riaz_card  = 'project';
	$riaz_grid  = 'projects-grid';
} elseif ( 'riaz_case_study' === $riaz_type ) {
	$riaz_title = __( 'More case studies', 'riaz-portfolio' );
	$riaz_card  = 'case-study';
	$riaz_grid  = 'cases-grid';
} elseif ( 'riaz_service' === $riaz_type ) {
	$riaz_args['posts_per_page'] = 6;
	$riaz_args['orderby']        = array(
		'menu_order' => 'ASC',
		'date'       => 'DESC',
	);
	$riaz_title                  = __( 'Other services', 'riaz-portfolio' );
	$riaz_card                   = 'service';
	$riaz_grid                   = 'services-grid';
} else {
	return;
}

$riaz_related = new WP_Query( $riaz_args );
if ( ! $riaz_related->have_posts() ) {
	return;
}
?>
<section class="related-section" aria-labelledby="related-title">
	<h2 id="related-title"><?php echo esc_html( $riaz_title ); ?></h2>
	<div class="<?php echo esc_attr( $riaz_grid ); ?>" data-stagger<?php echo 'service' === $riaz_card ? ' data-spotlight' : ''; ?>>
		<?php
		$riaz_index = 0;
		while ( $riaz_related->have_posts() ) {
			$riaz_related->the_post();
			get_template_part( 'template-parts/cards/' . $riaz_card, null, array( 'index' => $riaz_index++ ) );
		}
		wp_reset_postdata();
		?>
	</div>
</section>
