<?php
/**
 * Section: Pricing plans.
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;

$riaz_args  = wp_parse_args( isset( $args ) ? $args : array(), array( 'id' => 'pricing' ) );
$riaz_query = riaz_section_query( 'riaz_pricing', 6 );

if ( ! $riaz_query->have_posts() ) {
	riaz_admin_placeholder(
		__( 'Pricing', 'riaz-portfolio' ),
		__( 'Add a pricing plan to show this section.', 'riaz-portfolio' ),
		admin_url( 'post-new.php?post_type=riaz_pricing' ),
		__( 'Add a plan', 'riaz-portfolio' )
	);
	return;
}
?>
<section class="pricing-section" id="<?php echo esc_attr( $riaz_args['id'] ); ?>"<?php riaz_section_attrs( 'pricing' ); ?>>
	<div class="section-shell">
		<div class="section-topline" data-reveal><?php riaz_section_heading( riaz_heading_args( 'pricing' ) ); ?></div>
		<div class="pricing-grid" data-stagger style="--pricing-cols:<?php echo esc_attr( min( 3, $riaz_query->post_count ) ); ?>">
			<?php
			while ( $riaz_query->have_posts() ) :
				$riaz_query->the_post();
				$riaz_id       = get_the_ID();
				$riaz_featured = (bool) riaz_meta( $riaz_id, 'featured' );
				$riaz_features = array_filter( array_map( 'trim', explode( "\n", (string) riaz_meta( $riaz_id, 'features' ) ) ) );
				?>
				<article class="pricing-card lift<?php echo $riaz_featured ? ' pricing-card-featured' : ''; ?>" data-reveal-item>
					<?php if ( riaz_meta( $riaz_id, 'badge' ) ) : ?>
						<span class="pricing-badge"><?php echo esc_html( riaz_meta( $riaz_id, 'badge' ) ); ?></span>
					<?php endif; ?>
					<h3><?php the_title(); ?></h3>
					<?php if ( riaz_meta( $riaz_id, 'price' ) ) : ?>
						<p class="pricing-price">
							<strong><?php echo esc_html( riaz_meta( $riaz_id, 'price' ) ); ?></strong>
							<?php if ( riaz_meta( $riaz_id, 'period' ) ) : ?>
								<span><?php echo esc_html( riaz_meta( $riaz_id, 'period' ) ); ?></span>
							<?php endif; ?>
						</p>
					<?php endif; ?>
					<?php if ( has_excerpt() ) : ?>
						<p class="pricing-desc"><?php echo esc_html( get_the_excerpt() ); ?></p>
					<?php endif; ?>
					<?php if ( $riaz_features ) : ?>
						<ul class="pricing-features">
							<?php foreach ( $riaz_features as $riaz_feature ) : ?>
								<li><span class="check-dot" aria-hidden="true"><?php riaz_icon( 'check', 12, array( 'stroke' => 3 ) ); ?></span><?php echo esc_html( $riaz_feature ); ?></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
					<?php
					riaz_button(
						riaz_meta( $riaz_id, 'btn_label', __( 'Get started', 'riaz-portfolio' ) ),
						riaz_meta( $riaz_id, 'btn_url', '#contact' ),
						$riaz_featured ? 'light' : 'outline'
					);
					?>
				</article>
			<?php endwhile; ?>
			<?php wp_reset_postdata(); ?>
		</div>
		<?php if ( riaz_mod( 'pricing_note' ) ) : ?>
			<p class="pricing-note"><?php riaz_inline( riaz_mod( 'pricing_note' ) ); ?></p>
		<?php endif; ?>
	</div>
</section>
