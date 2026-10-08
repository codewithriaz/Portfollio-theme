<?php
/**
 * Logo or site title.
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;

$riaz_logo_id = (int) get_theme_mod( 'custom_logo' );
$riaz_pill    = riaz_mod_bool( 'logo_pill' ) ? ' has-pill' : '';
$riaz_width   = absint( riaz_mod( 'logo_width' ) );

if ( $riaz_logo_id && wp_attachment_is_image( $riaz_logo_id ) ) : ?>
	<a class="brand<?php echo esc_attr( $riaz_pill ); ?>" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
		<?php
		echo wp_get_attachment_image(
			$riaz_logo_id,
			'full',
			false,
			array(
				'class'         => 'custom-logo',
				'alt'           => get_bloginfo( 'name' ),
				'loading'       => 'eager',
				'decoding'      => 'async',
				'fetchpriority' => 'low',
				'sizes'         => '(max-width: 600px) 120px, ' . $riaz_width . 'px',
			)
		);
		?>
	</a>
<?php else : ?>
	<a class="brand brand-text<?php echo esc_attr( $riaz_pill ); ?>" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
		<span class="brand-text-label"><?php bloginfo( 'name' ); ?></span><span class="brand-dot" aria-hidden="true">.</span>
	</a>
	<?php
endif;
