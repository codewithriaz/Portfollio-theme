<?php
/**
 * Section: Contact details and form.
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;

$riaz_args      = wp_parse_args( isset( $args ) ? $args : array(), array( 'id' => 'get-in-touch' ) );
$riaz_email     = riaz_mod( 'contact_email' );
$riaz_phone     = riaz_mod( 'contact_phone' );
$riaz_address   = riaz_mod( 'contact_address' );
$riaz_hours     = riaz_mod( 'contact_hours' );
$riaz_shortcode = riaz_mod( 'contact_form_shortcode' );
?>
<section class="contact-section" id="<?php echo esc_attr( $riaz_args['id'] ); ?>"<?php riaz_section_attrs( 'contact' ); ?>>
	<div class="section-shell contact-grid">
		<div data-reveal="left">
			<?php riaz_section_heading( riaz_heading_args( 'contact' ) ); ?>
			<ul class="contact-details">
				<?php if ( $riaz_email ) : ?>
					<li>
						<span class="icon-tile" aria-hidden="true"><?php riaz_icon( 'mail', 20 ); ?></span>
						<span><small><?php esc_html_e( 'Email', 'riaz-portfolio' ); ?></small><a href="<?php echo esc_url( 'mailto:' . antispambot( $riaz_email ) ); ?>"><?php echo esc_html( antispambot( $riaz_email ) ); ?></a></span>
					</li>
				<?php endif; ?>
				<?php if ( $riaz_phone ) : ?>
					<li>
						<span class="icon-tile" aria-hidden="true"><?php riaz_icon( 'phone', 20 ); ?></span>
						<span><small><?php esc_html_e( 'Phone', 'riaz-portfolio' ); ?></small><a href="<?php echo esc_url( 'tel:' . preg_replace( '/[^0-9+]/', '', $riaz_phone ) ); ?>"><?php echo esc_html( $riaz_phone ); ?></a></span>
					</li>
				<?php endif; ?>
				<?php if ( $riaz_address ) : ?>
					<li>
						<span class="icon-tile" aria-hidden="true"><?php riaz_icon( 'map-pin', 20 ); ?></span>
						<span><small><?php esc_html_e( 'Location', 'riaz-portfolio' ); ?></small><strong><?php echo nl2br( esc_html( wp_strip_all_tags( $riaz_address ) ) ); ?></strong></span>
					</li>
				<?php endif; ?>
				<?php if ( $riaz_hours ) : ?>
					<li>
						<span class="icon-tile" aria-hidden="true"><?php riaz_icon( 'clock', 20 ); ?></span>
						<span><small><?php esc_html_e( 'Availability', 'riaz-portfolio' ); ?></small><strong><?php echo esc_html( $riaz_hours ); ?></strong></span>
					</li>
				<?php endif; ?>
			</ul>
			<?php riaz_social_list( 'is-light' ); ?>
		</div>
		<div class="contact-form-card" data-reveal="right">
			<?php
			if ( $riaz_shortcode ) {
				echo do_shortcode( $riaz_shortcode );
			} elseif ( riaz_mod_bool( 'contact_form_builtin' ) && function_exists( 'riaz_core_contact_form' ) ) {
				riaz_core_contact_form( array( 'note' => riaz_mod( 'contact_form_note' ) ) );
			} elseif ( $riaz_email ) {
				echo '<p class="section-description">' . esc_html__( 'The quickest way to reach me is by email.', 'riaz-portfolio' ) . '</p><p style="margin-top:24px">';
				riaz_button( __( 'Send an email', 'riaz-portfolio' ), 'mailto:' . antispambot( $riaz_email ), 'primary', array( 'icon' => 'mail' ) );
				echo '</p>';
			} elseif ( current_user_can( 'edit_theme_options' ) ) {
				esc_html_e( 'Activate the Riaz Portfolio Core plugin for the built-in form, add a form shortcode, or set a contact email in Theme Options → Contact Details.', 'riaz-portfolio' );
			}
			?>
		</div>
	</div>
</section>
