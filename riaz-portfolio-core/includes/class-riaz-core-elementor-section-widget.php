<?php
/**
 * Elementor widget: Theme Section.
 *
 * @package Riaz_Portfolio_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Renders any Riaz Portfolio section inside an Elementor layout.
 */
class Riaz_Core_Elementor_Section_Widget extends \Elementor\Widget_Base {

	/**
	 * Widget slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'riaz-section';
	}

	/**
	 * Widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Theme Section', 'riaz-portfolio-core' );
	}

	/**
	 * Widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-site-identity';
	}

	/**
	 * Widget categories.
	 *
	 * @return string[]
	 */
	public function get_categories() {
		return array( 'riaz-portfolio-core' );
	}

	/**
	 * Search keywords.
	 *
	 * @return string[]
	 */
	public function get_keywords() {
		return array( 'riaz', 'section', 'hero', 'about', 'services', 'portfolio', 'testimonials', 'faq', 'pricing', 'team' );
	}

	/**
	 * Controls.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array( 'label' => esc_html__( 'Section', 'riaz-portfolio-core' ) )
		);

		$this->add_control(
			'section',
			array(
				'label'   => esc_html__( 'Section', 'riaz-portfolio-core' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => riaz_get_section_choices(),
				'default' => 'services',
			)
		);

		$this->add_control(
			'anchor',
			array(
				'label'       => esc_html__( 'HTML anchor (optional)', 'riaz-portfolio-core' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => '',
				'label_block' => true,
			)
		);

		$this->add_control(
			'note',
			array(
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Edit this section’s content in Appearance → Customize → Homepage Sections, or in its post type (Services, Portfolio, FAQs…).', 'riaz-portfolio-core' ),
				'content_classes' => 'elementor-descriptor',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Front-end and editor output.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$slug     = isset( $settings['section'] ) ? sanitize_key( $settings['section'] ) : '';
		$args     = array( 'context' => 'elementor' );
		if ( ! empty( $settings['anchor'] ) ) {
			$args['id'] = sanitize_html_class( $settings['anchor'] );
		}
		$html = riaz_get_section_html( $slug, $args );
		if ( '' === trim( $html ) && \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			$html = '<div class="rp-placeholder">' . esc_html__( 'This section has no content yet. Add items in the WordPress admin.', 'riaz-portfolio-core' ) . '</div>';
		}
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- section templates escape their output.
	}
}
