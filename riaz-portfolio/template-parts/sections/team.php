<?php
/**
 * Section: Team members.
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;

$riaz_args  = wp_parse_args( isset( $args ) ? $args : array(), array( 'id' => 'team' ) );
$riaz_query = riaz_section_query( 'riaz_team', riaz_mod( 'team_count' ) );

if ( ! $riaz_query->have_posts() ) {
	riaz_admin_placeholder(
		__( 'Team', 'riaz-portfolio' ),
		__( 'Add a team member to show this section.', 'riaz-portfolio' ),
		admin_url( 'post-new.php?post_type=riaz_team' ),
		__( 'Add a team member', 'riaz-portfolio' )
	);
	return;
}
?>
<section class="team-section" id="<?php echo esc_attr( $riaz_args['id'] ); ?>"<?php riaz_section_attrs( 'team' ); ?>>
	<div class="section-shell">
		<div class="section-topline" data-reveal><?php riaz_section_heading( riaz_heading_args( 'team' ) ); ?></div>
		<div class="team-grid" data-stagger>
			<?php
			while ( $riaz_query->have_posts() ) :
				$riaz_query->the_post();
				$riaz_id    = get_the_ID();
				$riaz_name  = get_the_title();
				$riaz_links = array_filter(
					array(
						'linkedin' => riaz_meta( $riaz_id, 'linkedin' ),
						'x'        => riaz_meta( $riaz_id, 'x' ),
						'globe'    => riaz_meta( $riaz_id, 'website' ),
						'mail'     => riaz_meta( $riaz_id, 'email' ) ? 'mailto:' . antispambot( riaz_meta( $riaz_id, 'email' ) ) : '',
					)
				);
				$riaz_names = array(
					'linkedin' => 'LinkedIn',
					'x'        => 'X',
					'globe'    => __( 'Website', 'riaz-portfolio' ),
					'mail'     => __( 'Email', 'riaz-portfolio' ),
				);
				?>
				<article class="team-card" data-reveal-item>
					<div class="team-photo">
						<?php if ( has_post_thumbnail() ) : ?>
							<?php
							the_post_thumbnail(
								'riaz-portrait',
								array(
									/* translators: %s: person name. */
									'alt'     => sprintf( __( 'Photo of %s', 'riaz-portfolio' ), $riaz_name ),
									'loading' => 'lazy',
									'sizes'   => '(max-width: 600px) 92vw, (max-width: 1100px) 46vw, 300px',
								)
							);
							?>
						<?php else : ?>
							<span class="team-initials" aria-hidden="true"><?php echo esc_html( riaz_initials( $riaz_name ) ); ?></span>
						<?php endif; ?>
						<?php if ( $riaz_links ) : ?>
							<ul class="team-social">
								<?php foreach ( $riaz_links as $riaz_icon_name => $riaz_url ) : ?>
									<li>
										<a href="<?php echo esc_url( $riaz_url ); ?>"<?php echo 'mail' === $riaz_icon_name ? '' : ' target="_blank" rel="noopener noreferrer"'; ?>>
											<?php riaz_icon( $riaz_icon_name, 16 ); ?>
											<span class="screen-reader-text"><?php echo esc_html( $riaz_name . ' — ' . $riaz_names[ $riaz_icon_name ] ); ?></span>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</div>
					<div class="team-body">
						<h3><?php echo esc_html( $riaz_name ); ?></h3>
						<?php if ( riaz_meta( $riaz_id, 'role' ) ) : ?>
							<p class="team-role"><?php echo esc_html( riaz_meta( $riaz_id, 'role' ) ); ?></p>
						<?php endif; ?>
						<?php if ( get_the_content() ) : ?>
							<p><?php echo esc_html( wp_trim_words( wp_strip_all_tags( get_the_content() ), 22 ) ); ?></p>
						<?php endif; ?>
					</div>
				</article>
			<?php endwhile; ?>
			<?php wp_reset_postdata(); ?>
		</div>
	</div>
</section>
