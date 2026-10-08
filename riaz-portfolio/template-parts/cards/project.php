<?php
/**
 * Card: Portfolio project (used in the loop).
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;

$riaz_terms = get_the_terms( get_the_ID(), 'riaz_portfolio_cat' );
$riaz_terms = ( $riaz_terms && ! is_wp_error( $riaz_terms ) ) ? $riaz_terms : array();
?>
<article class="project-card" data-reveal-item data-terms="<?php echo esc_attr( implode( ' ', wp_list_pluck( $riaz_terms, 'slug' ) ) ); ?>">
	<?php riaz_card_media( 'riaz-card' ); ?>
	<div class="project-body">
		<?php if ( $riaz_terms ) : ?>
			<ul class="chip-list">
				<?php foreach ( array_slice( $riaz_terms, 0, 3 ) as $riaz_term ) : ?>
					<li><?php echo esc_html( $riaz_term->name ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
		<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<?php if ( has_excerpt() || get_the_content() ) : ?>
			<p><?php echo esc_html( wp_strip_all_tags( get_the_excerpt() ) ); ?></p>
		<?php endif; ?>
	</div>
</article>
