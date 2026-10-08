<?php
/**
 * Customizer registration, sanitization and live preview.
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register panels, sections, settings, controls and partials from the schema.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function riaz_customize_register( $wp_customize ) {
	require_once RIAZ_DIR . '/inc/customizer/controls/class-riaz-repeater-control.php';
	require_once RIAZ_DIR . '/inc/customizer/controls/class-riaz-sortable-control.php';
	require_once RIAZ_DIR . '/inc/customizer/controls/class-riaz-info-control.php';

	$wp_customize->get_setting( 'blogname' )->transport        = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';

	$schema   = riaz_customizer_schema();
	$priority = 10;

	foreach ( $schema['panels'] as $id => $panel ) {
		$wp_customize->add_panel( $id, $panel );
	}

	$partials = array();

	foreach ( $schema['sections'] as $section_id => $section ) {
		$wp_customize->add_section(
			$section_id,
			array(
				'title'       => $section['title'],
				'panel'       => $section['panel'],
				'description' => isset( $section['description'] ) ? $section['description'] : '',
				'priority'    => $priority++,
			)
		);

		foreach ( $section['fields'] as $key => $field ) {
			$control_args = array(
				'label'       => isset( $field['label'] ) ? $field['label'] : '',
				'description' => isset( $field['description'] ) ? $field['description'] : '',
				'section'     => $section_id,
				'priority'    => $priority++,
			);

			if ( 'info' === $field['type'] ) {
				$wp_customize->add_control(
					new Riaz_Info_Control(
						$wp_customize,
						$key,
						array_merge(
							$control_args,
							array(
								'settings'   => array(),
								'capability' => 'edit_theme_options',
							)
						)
					)
				);
				continue;
			}

			$transport = isset( $field['transport'] ) ? $field['transport'] : ( empty( $section['partial'] ) ? 'refresh' : 'postMessage' );

			$wp_customize->add_setting(
				$key,
				array(
					'default'           => riaz_default( $key ),
					'type'              => 'theme_mod',
					'capability'        => 'edit_theme_options',
					'transport'         => $transport,
					'sanitize_callback' => 'riaz_sanitize_setting',
				)
			);

			switch ( $field['type'] ) {
				case 'color':
					$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $key, $control_args ) );
					break;
				case 'image':
					$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, $key, array_merge( $control_args, array( 'mime_type' => 'image' ) ) ) );
					break;
				case 'repeater':
					$wp_customize->add_control(
						new Riaz_Repeater_Control(
							$wp_customize,
							$key,
							array_merge(
								$control_args,
								array(
									'fields'    => $field['fields'],
									'max'       => isset( $field['max'] ) ? $field['max'] : 0,
									'add_label' => isset( $field['add_label'] ) ? $field['add_label'] : __( 'Add item', 'riaz-portfolio' ),
								)
							)
						)
					);
					break;
				case 'sortable':
					$wp_customize->add_control( new Riaz_Sortable_Control( $wp_customize, $key, $control_args ) );
					break;
				case 'select':
					$wp_customize->add_control(
						$key,
						array_merge(
							$control_args,
							array(
								'type'    => 'select',
								'choices' => $field['choices'],
							)
						)
					);
					break;
				case 'range':
				case 'number':
					$wp_customize->add_control(
						$key,
						array_merge(
							$control_args,
							array(
								'type'        => $field['type'],
								'input_attrs' => array(
									'min'  => isset( $field['min'] ) ? $field['min'] : 0,
									'max'  => isset( $field['max'] ) ? $field['max'] : 100,
									'step' => isset( $field['step'] ) ? $field['step'] : 1,
								),
							)
						)
					);
					break;
				case 'textarea':
				case 'checkbox':
				case 'email':
				case 'url':
				case 'text':
				default:
					$wp_customize->add_control( $key, array_merge( $control_args, array( 'type' => $field['type'] ) ) );
			}

			if ( ! empty( $section['partial'] ) && 'postMessage' === $transport ) {
				$partials[ $section['partial'] ][] = $key;
			}
		}
	}

	if ( isset( $wp_customize->selective_refresh ) ) {
		foreach ( $partials as $slug => $settings ) {
			$wp_customize->selective_refresh->add_partial(
				'riaz_section_' . $slug,
				array(
					'selector'            => '[data-riaz-section="' . $slug . '"]',
					'settings'            => $settings,
					'container_inclusive' => true,
					'fallback_refresh'    => true,
					'render_callback'     => static function () use ( $slug ) {
						return riaz_get_section_html( $slug );
					},
				)
			);
		}

		$wp_customize->selective_refresh->add_partial(
			'blogname',
			array(
				'selector'        => '.footer-brand',
				'render_callback' => static function () {
					return esc_html( riaz_brand_name() ) . '<span>.</span>';
				},
			)
		);
	}
}
add_action( 'customize_register', 'riaz_customize_register' );

/**
 * Sanitize any theme setting according to its schema type.
 *
 * @param mixed                $value   Raw value.
 * @param WP_Customize_Setting $setting Setting.
 * @return mixed
 */
function riaz_sanitize_setting( $value, $setting ) {
	$fields = riaz_customizer_fields();
	if ( ! isset( $fields[ $setting->id ] ) ) {
		return null;
	}
	$field = $fields[ $setting->id ];

	switch ( $field['type'] ) {
		case 'checkbox':
			return (bool) $value;
		case 'color':
			$color = sanitize_hex_color( $value );
			return $color ? $color : $field['default'];
		case 'image':
			return absint( $value );
		case 'number':
		case 'range':
			$number = is_numeric( $value ) ? (int) $value : (int) $field['default'];
			$min    = isset( $field['min'] ) ? (int) $field['min'] : $number;
			$max    = isset( $field['max'] ) ? (int) $field['max'] : $number;
			return max( $min, min( $max, $number ) );
		case 'select':
			return array_key_exists( (string) $value, $field['choices'] ) ? (string) $value : $field['default'];
		case 'url':
			return esc_url_raw( trim( (string) $value ) );
		case 'email':
			return sanitize_email( (string) $value );
		case 'textarea':
			return wp_kses_post( (string) $value );
		case 'repeater':
			return riaz_sanitize_repeater( $value, $field );
		case 'sortable':
			return riaz_sanitize_section_order( $value );
		case 'text':
		default:
			if ( 'ga4_id' === $setting->id ) {
				$value = strtoupper( trim( (string) $value ) );
				return preg_match( '/^G-[A-Z0-9]{4,20}$/', $value ) ? $value : '';
			}
			if ( 'contact_form_shortcode' === $setting->id ) {
				$value = trim( wp_strip_all_tags( (string) $value ) );
				return preg_match( '/^\[[^\[\]]+\]$/', $value ) ? $value : '';
			}
			return wp_kses( (string) $value, riaz_inline_kses() );
	}
}

/**
 * Sanitize a repeater: JSON list of rows with known keys only.
 *
 * @param mixed $value Raw JSON.
 * @param array $field Field definition.
 * @return string JSON.
 */
function riaz_sanitize_repeater( $value, $field ) {
	$rows = is_array( $value ) ? $value : json_decode( (string) $value, true );
	if ( ! is_array( $rows ) ) {
		return '[]';
	}
	$clean = array();
	$icons = riaz_icon_choices();
	foreach ( $rows as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}
		$item = array();
		foreach ( $field['fields'] as $sub_key => $sub ) {
			$raw = isset( $row[ $sub_key ] ) ? $row[ $sub_key ] : '';
			if ( is_array( $raw ) ) {
				$raw = '';
			}
			switch ( $sub['type'] ) {
				case 'icon':
					$item[ $sub_key ] = array_key_exists( (string) $raw, $icons ) ? (string) $raw : 'sparkles';
					break;
				case 'textarea':
					$item[ $sub_key ] = sanitize_textarea_field( (string) $raw );
					break;
				case 'url':
					$item[ $sub_key ] = esc_url_raw( (string) $raw );
					break;
				default:
					$item[ $sub_key ] = sanitize_text_field( (string) $raw );
			}
		}
		if ( implode( '', array_diff_key( $item, array( 'icon' => true ) ) ) !== '' ) {
			$clean[] = $item;
		}
		if ( ! empty( $field['max'] ) && count( $clean ) >= (int) $field['max'] ) {
			break;
		}
	}
	return wp_json_encode( $clean );
}

/**
 * Sanitize the section order list.
 *
 * @param mixed $value Raw JSON.
 * @return string JSON.
 */
function riaz_sanitize_section_order( $value ) {
	$rows  = is_array( $value ) ? $value : json_decode( (string) $value, true );
	$valid = riaz_get_sections();
	$clean = array();
	$seen  = array();
	if ( is_array( $rows ) ) {
		foreach ( $rows as $row ) {
			if ( ! is_array( $row ) || empty( $row['id'] ) || ! isset( $valid[ $row['id'] ] ) || isset( $seen[ $row['id'] ] ) ) {
				continue;
			}
			$seen[ $row['id'] ] = true;
			$clean[]            = array(
				'id' => $row['id'],
				'on' => ! empty( $row['on'] ),
			);
		}
	}
	foreach ( riaz_default_section_order() as $row ) {
		if ( ! isset( $seen[ $row['id'] ] ) ) {
			$clean[] = $row;
		}
	}
	return wp_json_encode( $clean );
}

/**
 * Customizer control scripts and styles.
 */
function riaz_customize_controls_assets() {
	wp_enqueue_style( 'riaz-customizer', riaz_asset( 'css/customizer.css' ), array(), riaz_asset_version( 'assets/css/customizer.css' ) );
	wp_enqueue_script(
		'riaz-customizer-controls',
		riaz_asset( 'js/customizer-controls.js' ),
		array( 'customize-controls', 'jquery', 'jquery-ui-sortable' ),
		riaz_asset_version( 'assets/js/customizer-controls.js' ),
		true
	);
	wp_localize_script(
		'riaz-customizer-controls',
		'riazCustomizer',
		array(
			'icons'    => riaz_icon_choices(),
			'sections' => riaz_get_sections(),
			'i18n'     => array(
				'remove'   => __( 'Remove', 'riaz-portfolio' ),
				'moveUp'   => __( 'Move up', 'riaz-portfolio' ),
				'moveDown' => __( 'Move down', 'riaz-portfolio' ),
				'item'     => __( 'Item', 'riaz-portfolio' ),
				'edit'     => __( 'Edit', 'riaz-portfolio' ),
				'max'      => __( 'Maximum reached', 'riaz-portfolio' ),
			),
		)
	);
}
add_action( 'customize_controls_enqueue_scripts', 'riaz_customize_controls_assets' );

/**
 * Live preview script (colors, site title, re-init of JS after partial refresh).
 */
function riaz_customize_preview_assets() {
	wp_enqueue_script(
		'riaz-customizer-preview',
		riaz_asset( 'js/customizer-preview.js' ),
		array( 'customize-preview', 'customize-selective-refresh' ),
		riaz_asset_version( 'assets/js/customizer-preview.js' ),
		true
	);
	$vars = array();
	foreach ( riaz_customizer_fields() as $key => $field ) {
		if ( 'color' === $field['type'] && ! empty( $field['css_var'] ) ) {
			$vars[ $key ] = $field['css_var'];
		}
	}
	wp_localize_script( 'riaz-customizer-preview', 'riazPreview', array( 'colorVars' => $vars ) );
}
add_action( 'customize_preview_init', 'riaz_customize_preview_assets' );
