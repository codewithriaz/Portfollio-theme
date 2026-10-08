<?php
/**
 * Section: Testimonials (video carousel + written quotes).
 *
 * Video testimonials show the YouTube thumbnail and only load the player
 * (from youtube-nocookie.com) when a visitor presses play.
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;

$riaz_args  = wp_parse_args( isset( $args ) ? $args : array(), array( 'id' => 'testimonials' ) );
$riaz_query = riaz_section_query( 'riaz_testimonial', riaz_mod( 'testimonials_count' ) );

if ( ! $riaz_query->have_posts() ) {
	riaz_admin_placeholder(
		__( 'Testimonials', 'riaz-portfolio' ),
		__( 'Add a real client testimonial (written or YouTube video) to show this section.', 'riaz-portfolio' ),
		admin_url( 'post-new.php?post_type=riaz_testimonial' ),
		__( 'Add a testimonial', 'riaz-portfolio' )
	);
	return;
}

$riaz_videos = '';
$riaz_quotes = '';

while ( $riaz_query->have_posts() ) {
	$riaz_query->the_post();
	$riaz_id     = get_the_ID();
	$riaz_name   = get_the_title();
	$riaz_role   = riaz_meta( $riaz_id, 'role' );
	$riaz_video  = riaz_youtube_id( riaz_meta( $riaz_id, 'youtube' ) );
	$riaz_rating = (int) riaz_meta( $riaz_id, 'rating', 0 );

	ob_start();
	if ( $riaz_video ) {
		$riaz_custom = has_post_thumbnail();
		$riaz_thumb  = $riaz_custom ? get_the_post_thumbnail_url( $riaz_id, 'riaz-portrait' ) : 'https://i.ytimg.com/vi/' . $riaz_video . '/hqdefault.jpg';
		?>
		<div class="carousel-slide" data-reveal-item>
			<div class="video-frame<?php echo $riaz_custom ? ' has-custom-thumb' : ''; ?>" data-youtube="<?php echo esc_attr( $riaz_video ); ?>" data-title="<?php echo esc_attr( $riaz_name ); ?>">
				<img class="video-thumb" src="<?php echo esc_url( $riaz_thumb ); ?>" alt="" width="480" height="360" loading="lazy" decoding="async">
				<button type="button" class="video-play" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: client name. */ __( 'Play video testimonial from %s', 'riaz-portfolio' ), $riaz_name ) ); ?>">
					<?php riaz_icon( 'play', 24 ); ?>
				</button>
				<span class="video-caption">
					<strong><?php echo esc_html( $riaz_name ); ?></strong>
					<?php if ( $riaz_role ) : ?>
						<small><?php echo esc_html( $riaz_role ); ?></small>
					<?php endif; ?>
				</span>
			</div>
		</div>
		<?php
		$riaz_videos .= ob_get_clean();
	} else {
		?>
		<div class="carousel-slide is-wide" data-reveal-item>
			<figure class="quote-card lift">
				<div class="quote-card-top">
					<span class="quote-icon" aria-hidden="true"><?php riaz_icon( 'quote', 20 ); ?></span>
					<?php if ( $riaz_rating ) : ?>
						<span class="rating" role="img" aria-label="<?php echo esc_attr( sprintf( /* translators: %d: rating. */ __( 'Rated %d out of 5', 'riaz-portfolio' ), $riaz_rating ) ); ?>">
							<?php for ( $riaz_star = 1; $riaz_star <= 5; $riaz_star++ ) : ?>
								<?php riaz_icon( 'star', 16, array( 'class' => $riaz_star > $riaz_rating ? 'is-empty' : '' ) ); ?>
							<?php endfor; ?>
						</span>
					<?php endif; ?>
				</div>
				<blockquote><?php the_content(); ?></blockquote>
				<figcaption>
					<?php if ( has_post_thumbnail() ) : ?>
						<?php
						the_post_thumbnail(
							'thumbnail',
							array(
								'alt'     => '',
								'loading' => 'lazy',
							)
						);
						?>
					<?php else : ?>
						<span class="avatar-initials" aria-hidden="true"><?php echo esc_html( riaz_initials( $riaz_name ) ); ?></span>
					<?php endif; ?>
					<span>
						<strong><?php echo esc_html( $riaz_name ); ?></strong>
						<?php if ( $riaz_role ) : ?>
							<small><?php echo esc_html( $riaz_role ); ?></small>
						<?php endif; ?>
					</span>
				</figcaption>
			</figure>
		</div>
		<?php
		$riaz_quotes .= ob_get_clean();
	}
}
wp_reset_postdata();
?>
<section class="testimonials-section section-shell" id="<?php echo esc_attr( $riaz_args['id'] ); ?>"<?php riaz_section_attrs( 'testimonials' ); ?>>
	<div data-reveal><?php riaz_section_heading( riaz_heading_args( 'testimonials' ) ); ?></div>
	<?php
	if ( $riaz_videos ) {
		get_template_part(
			'template-parts/components/carousel',
			null,
			array(
				'label'  => __( 'Video testimonials', 'riaz-portfolio' ),
				'slides' => $riaz_videos,
			)
		);
	}
	if ( $riaz_quotes ) {
		get_template_part(
			'template-parts/components/carousel',
			null,
			array(
				'label'  => __( 'Written testimonials', 'riaz-portfolio' ),
				'slides' => $riaz_quotes,
			)
		);
	}
	?>
</section>
