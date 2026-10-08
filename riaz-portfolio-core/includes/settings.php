<?php
/**
 * Plugin settings (SEO, analytics, performance), stored in one option so
 * they survive a change of theme, and edited in the Customizer.
 *
 * @package Riaz_Portfolio_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Setting definitions.
 *
 * @return array
 */
function riaz_core_settings_schema() {
	return array(
		'seo_enable'      => array(
			'type'    => 'checkbox',
			'label'   => __( 'Output meta description, Open Graph and schema', 'riaz-portfolio-core' ),
			'default' => true,
		),
		'seo_description' => array(
			'type'    => 'textarea',
			'label'   => __( 'Homepage meta description', 'riaz-portfolio-core' ),
			'default' => __( 'Thoughtful digital strategy, SEO, and content to help ambitious businesses build a stronger online presence and grow with clarity.', 'riaz-portfolio-core' ),
		),
		'seo_entity'      => array(
			'type'    => 'select',
			'label'   => __( 'This website represents', 'riaz-portfolio-core' ),
			'default' => 'person',
			'choices' => array(
				'person'       => __( 'A person', 'riaz-portfolio-core' ),
				'organization' => __( 'An organization', 'riaz-portfolio-core' ),
			),
		),
		'seo_entity_name' => array(
			'type'        => 'text',
			'label'       => __( 'Person / organization name', 'riaz-portfolio-core' ),
			'description' => __( 'Leave empty to use the site title.', 'riaz-portfolio-core' ),
			'default'     => '',
		),
		'seo_job_title'   => array(
			'type'    => 'text',
			'label'   => __( 'Job title (person only)', 'riaz-portfolio-core' ),
			'default' => __( 'Digital Growth Partner', 'riaz-portfolio-core' ),
		),
		'seo_share_image' => array(
			'type'        => 'image',
			'label'       => __( 'Default social share image', 'riaz-portfolio-core' ),
			'description' => __( '1200×630px. Used when a page has no featured image.', 'riaz-portfolio-core' ),
			'default'     => 0,
		),
		'seo_x_handle'    => array(
			'type'        => 'text',
			'label'       => __( 'X (Twitter) username', 'riaz-portfolio-core' ),
			'description' => __( 'Without the @.', 'riaz-portfolio-core' ),
			'default'     => '',
		),
		'ga4_id'          => array(
			'type'        => 'text',
			'label'       => __( 'Google Analytics 4 measurement ID', 'riaz-portfolio-core' ),
			'description' => __( 'For example G-XXXXXXXXXX. Leave empty to disable. Logged-in editors are not tracked.', 'riaz-portfolio-core' ),
			'default'     => '',
		),
		'perf_emoji'      => array(
			'type'        => 'checkbox',
			'label'       => __( 'Remove the WordPress emoji script', 'riaz-portfolio-core' ),
			'description' => __( 'Browsers render emoji natively.', 'riaz-portfolio-core' ),
			'default'     => true,
		),
		'perf_generator'  => array(
			'type'    => 'checkbox',
			'label'   => __( 'Hide the WordPress version meta tag', 'riaz-portfolio-core' ),
			'default' => true,
		),
	);
}

/**
 * Read a plugin setting.
 *
 * @param string $key Setting key.
 * @return mixed
 */
function riaz_core_option( $key ) {
	$saved  = get_option( 'riaz_core_settings', array() );
	$schema = riaz_core_settings_schema();
	if ( is_array( $saved ) && array_key_exists( $key, $saved ) ) {
		return $saved[ $key ];
	}
	return isset( $schema[ $key ] ) ? $schema[ $key ]['default'] : '';
}

/**
 * Customizer section. Joins the theme's "Theme Options" panel when present.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function riaz_core_customize_register( $wp_customize ) {
	$panel = $wp_customize->get_panel( 'riaz_options' ) ? 'riaz_options' : '';

	$wp_customize->add_section(
		'riaz_core_seo',
		array(
			'title'       => __( 'SEO, Analytics & Performance', 'riaz-portfolio-core' ),
			'description' => __( 'Built-in meta tags, Open Graph and schema switch off automatically when Yoast SEO, Rank Math, SEOPress, All in One SEO, Slim SEO or The SEO Framework is active.', 'riaz-portfolio-core' ),
			'panel'       => $panel,
			'priority'    => 200,
		)
	);

	foreach ( riaz_core_settings_schema() as $key => $field ) {
		$id = 'riaz_core_settings[' . $key . ']';
		$wp_customize->add_setting(
			$id,
			array(
				'type'              => 'option',
				'default'           => $field['default'],
				'capability'        => 'manage_options',
				'sanitize_callback' => static function ( $value ) use ( $key, $field ) {
					return riaz_core_sanitize_setting( $key, $value, $field );
				},
			)
		);
		$args = array(
			'label'       => $field['label'],
			'description' => isset( $field['description'] ) ? $field['description'] : '',
			'section'     => 'riaz_core_seo',
		);
		if ( 'image' === $field['type'] ) {
			$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, $id, array_merge( $args, array( 'mime_type' => 'image' ) ) ) );
		} elseif ( 'select' === $field['type'] ) {
			$wp_customize->add_control(
				$id,
				array_merge(
					$args,
					array(
						'type'    => 'select',
						'choices' => $field['choices'],
					)
				)
			);
		} else {
			$wp_customize->add_control( $id, array_merge( $args, array( 'type' => $field['type'] ) ) );
		}
	}
}
add_action( 'customize_register', 'riaz_core_customize_register', 20 );

/**
 * Sanitize one setting.
 *
 * @param string $key   Key.
 * @param mixed  $value Raw value.
 * @param array  $field Definition.
 * @return mixed
 */
function riaz_core_sanitize_setting( $key, $value, $field ) {
	switch ( $field['type'] ) {
		case 'checkbox':
			return (bool) $value;
		case 'image':
			return absint( $value );
		case 'select':
			return array_key_exists( (string) $value, $field['choices'] ) ? (string) $value : $field['default'];
		case 'textarea':
			return sanitize_textarea_field( (string) $value );
		default:
			if ( 'ga4_id' === $key ) {
				$value = strtoupper( trim( (string) $value ) );
				return preg_match( '/^G-[A-Z0-9]{4,20}$/', $value ) ? $value : '';
			}
			return sanitize_text_field( (string) $value );
	}
}
