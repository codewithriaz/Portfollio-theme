<?php
/**
 * Read-only heading/notice control (no setting).
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * Info control.
 */
class Riaz_Info_Control extends WP_Customize_Control {

	/**
	 * Control type.
	 *
	 * @var string
	 */
	public $type = 'riaz-info';

	/**
	 * Render the notice.
	 */
	public function render_content() {
		?>
		<div class="riaz-info">
			<?php if ( $this->label ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif; ?>
			<?php if ( $this->description ) : ?>
				<p class="description"><?php echo wp_kses_post( $this->description ); ?></p>
			<?php endif; ?>
		</div>
		<?php
	}
}
