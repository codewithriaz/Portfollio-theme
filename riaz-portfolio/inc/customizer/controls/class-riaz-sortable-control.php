<?php
/**
 * Sortable checklist control used for homepage section order & visibility.
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * Sortable control.
 */
class Riaz_Sortable_Control extends WP_Customize_Control {

	/**
	 * Control type.
	 *
	 * @var string
	 */
	public $type = 'riaz-sortable';

	/**
	 * Markup; the list is built by assets/js/customizer-controls.js.
	 */
	public function render_content() {
		?>
		<?php if ( $this->label ) : ?>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
		<?php endif; ?>
		<?php if ( $this->description ) : ?>
			<span class="description customize-control-description"><?php echo wp_kses_post( $this->description ); ?></span>
		<?php endif; ?>
		<ul class="riaz-sortable"></ul>
		<?php
	}
}
