<?php
/**
 * Section: FAQ accordion.
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;

$riaz_args  = wp_parse_args( isset( $args ) ? $args : array(), array( 'id' => 'faq' ) );
$riaz_query = riaz_section_query( 'riaz_faq', riaz_mod( 'faq_count' ) );

if ( ! $riaz_query->have_posts() ) {
	riaz_admin_placeholder(
		__( 'FAQ', 'riaz-portfolio' ),
		__( 'Add a question to show this section, or import the starter content.', 'riaz-portfolio' ),
		admin_url( 'post-new.php?post_type=riaz_faq' ),
		__( 'Add a question', 'riaz-portfolio' )
	);
	return;
}
$riaz_first_open = riaz_mod_bool( 'faq_first_open' );
?>
<section class="faq-section" id="<?php echo esc_attr( $riaz_args['id'] ); ?>"<?php riaz_section_attrs( 'faq' ); ?>>
	<div class="section-shell faq-inner">
		<div class="faq-intro" data-reveal="left">
			<?php riaz_section_heading( riaz_heading_args( 'faq' ) ); ?>
			<?php if ( riaz_mod( 'faq_help_title' ) ) : ?>
				<div class="faq-help">
					<span class="faq-help-icon" aria-hidden="true"><?php riaz_icon( 'mail', 18 ); ?></span>
					<div>
						<strong><?php echo esc_html( riaz_mod( 'faq_help_title' ) ); ?></strong>
						<?php riaz_link_arrow( riaz_mod( 'faq_help_label' ), riaz_mod( 'faq_help_url' ) ); ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
		<div class="faq-list" data-stagger>
			<?php
			$riaz_index = 0;
			while ( $riaz_query->have_posts() ) :
				$riaz_query->the_post();
				?>
				<div data-reveal-item>
					<details class="faq-item"<?php echo ( 0 === $riaz_index && $riaz_first_open ) ? ' open' : ''; ?> data-accordion>
						<summary><?php the_title(); ?><span class="faq-icon" aria-hidden="true"></span></summary>
						<div class="faq-answer"><div class="faq-answer-inner"><?php the_content(); ?></div></div>
					</details>
				</div>
				<?php
				++$riaz_index;
			endwhile;
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>
