<?php
/**
 * Repeater control: an editable, sortable list of small rows stored as JSON.
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * Repeater control.
 */
class Riaz_Repeater_Control extends WP_Customize_Control {

	/**
	 * Control type.
	 *
	 * @var string
	 */
	public $type = 'riaz-repeater';

	/**
	 * Sub-field definitions: key => { type, label }.
	 *
	 * @var array
	 */
	public $fields = array();

	/**
	 * Maximum rows (0 = unlimited).
	 *
	 * @var int
	 */
	public $max = 0;

	/**
	 * Add button label.
	 *
	 * @var string
	 */
	public $add_label = '';

	/**
	 * Data passed to the JS control.
	 */
	public function to_json() {
		parent::to_json();
		$this->json['fields']   = $this->fields;
		$this->json['max']      = (int) $this->max;
		$this->json['addLabel'] = $this->add_label;
	}

	/**
	 * Markup; rows are built by assets/js/customizer-controls.js.
	 */
	public function render_content() {
		?>
		<?php if ( $this->label ) : ?>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
		<?php endif; ?>
		<?php if ( $this->description ) : ?>
			<span class="description customize-control-description"><?php echo wp_kses_post( $this->description ); ?></span>
		<?php endif; ?>
		<div class="riaz-repeater">
			<ul class="riaz-repeater-list"></ul>
			<button type="button" class="button riaz-repeater-add"><?php echo esc_html( $this->add_label ); ?></button>
		</div>
		<?php
	}
}
