<?php
/**
 * Meta boxes for the content types.
 *
 * Fields are declared once and used for rendering, saving (nonce +
 * capability checks + sanitization) and REST registration.
 *
 * @package Riaz_Portfolio_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Meta box definitions keyed by box ID.
 *
 * @return array
 */
function riaz_core_meta_boxes() {
	return array(
		'riaz_service_details'     => array(
			'title'      => __( 'Service details', 'riaz-portfolio-core' ),
			'post_types' => array( 'riaz_service' ),
			'context'    => 'side',
			'fields'     => array(
				'icon'     => array(
					'type'  => 'icon',
					'label' => __( 'Icon', 'riaz-portfolio-core' ),
				),
				'featured' => array(
					'type'  => 'checkbox',
					'label' => __( 'Highlight this card (blue gradient)', 'riaz-portfolio-core' ),
				),
				'link_url' => array(
					'type'        => 'url',
					'label'       => __( 'Custom "Learn more" link', 'riaz-portfolio-core' ),
					'description' => __( 'Optional. By default the card links to this service page when it has content.', 'riaz-portfolio-core' ),
				),
			),
		),
		'riaz_project_details'     => array(
			'title'      => __( 'Project details', 'riaz-portfolio-core' ),
			'post_types' => array( 'riaz_portfolio' ),
			'context'    => 'side',
			'fields'     => array(
				'client'      => array(
					'type'  => 'text',
					'label' => __( 'Client', 'riaz-portfolio-core' ),
				),
				'role'        => array(
					'type'  => 'text',
					'label' => __( 'Services / role', 'riaz-portfolio-core' ),
				),
				'year'        => array(
					'type'  => 'text',
					'label' => __( 'Year', 'riaz-portfolio-core' ),
				),
				'project_url' => array(
					'type'  => 'url',
					'label' => __( 'Live project URL', 'riaz-portfolio-core' ),
				),
			),
		),
		'riaz_case_details'        => array(
			'title'      => __( 'Case study details', 'riaz-portfolio-core' ),
			'post_types' => array( 'riaz_case_study' ),
			'context'    => 'normal',
			'fields'     => array(
				'client'        => array(
					'type'  => 'text',
					'label' => __( 'Client', 'riaz-portfolio-core' ),
				),
				'industry'      => array(
					'type'  => 'text',
					'label' => __( 'Industry', 'riaz-portfolio-core' ),
				),
				'duration'      => array(
					'type'  => 'text',
					'label' => __( 'Timeline', 'riaz-portfolio-core' ),
				),
				'metric1_value' => array(
					'type'        => 'text',
					'label'       => __( 'Result 1 — value', 'riaz-portfolio-core' ),
					'description' => __( 'Only use results you can verify, e.g. "+38%".', 'riaz-portfolio-core' ),
				),
				'metric1_label' => array(
					'type'  => 'text',
					'label' => __( 'Result 1 — label', 'riaz-portfolio-core' ),
				),
				'metric2_value' => array(
					'type'  => 'text',
					'label' => __( 'Result 2 — value', 'riaz-portfolio-core' ),
				),
				'metric2_label' => array(
					'type'  => 'text',
					'label' => __( 'Result 2 — label', 'riaz-portfolio-core' ),
				),
				'metric3_value' => array(
					'type'  => 'text',
					'label' => __( 'Result 3 — value', 'riaz-portfolio-core' ),
				),
				'metric3_label' => array(
					'type'  => 'text',
					'label' => __( 'Result 3 — label', 'riaz-portfolio-core' ),
				),
			),
		),
		'riaz_testimonial_details' => array(
			'title'      => __( 'Testimonial details', 'riaz-portfolio-core' ),
			'post_types' => array( 'riaz_testimonial' ),
			'context'    => 'normal',
			'fields'     => array(
				'role'    => array(
					'type'  => 'text',
					'label' => __( 'Role & company', 'riaz-portfolio-core' ),
				),
				'youtube' => array(
					'type'        => 'url',
					'label'       => __( 'YouTube video URL', 'riaz-portfolio-core' ),
					'description' => __( 'Optional. Add a YouTube or Shorts link to make this a video testimonial. Leave empty for a written quote (write it in the editor above).', 'riaz-portfolio-core' ),
				),
				'rating'  => array(
					'type'    => 'select',
					'label'   => __( 'Star rating', 'riaz-portfolio-core' ),
					'choices' => array(
						''  => __( 'No rating', 'riaz-portfolio-core' ),
						'5' => '★★★★★',
						'4' => '★★★★',
						'3' => '★★★',
					),
				),
			),
		),
		'riaz_team_details'        => array(
			'title'      => __( 'Team member details', 'riaz-portfolio-core' ),
			'post_types' => array( 'riaz_team' ),
			'context'    => 'normal',
			'fields'     => array(
				'role'     => array(
					'type'  => 'text',
					'label' => __( 'Role', 'riaz-portfolio-core' ),
				),
				'linkedin' => array(
					'type'  => 'url',
					'label' => 'LinkedIn',
				),
				'x'        => array(
					'type'  => 'url',
					'label' => 'X (Twitter)',
				),
				'website'  => array(
					'type'  => 'url',
					'label' => __( 'Website', 'riaz-portfolio-core' ),
				),
				'email'    => array(
					'type'  => 'email',
					'label' => __( 'Email', 'riaz-portfolio-core' ),
				),
			),
		),
		'riaz_pricing_details'     => array(
			'title'      => __( 'Plan details', 'riaz-portfolio-core' ),
			'post_types' => array( 'riaz_pricing' ),
			'context'    => 'normal',
			'fields'     => array(
				'price'     => array(
					'type'        => 'text',
					'label'       => __( 'Price', 'riaz-portfolio-core' ),
					'description' => __( 'Include the currency, e.g. "$490" or "Custom".', 'riaz-portfolio-core' ),
				),
				'period'    => array(
					'type'  => 'text',
					'label' => __( 'Billing period (e.g. / month)', 'riaz-portfolio-core' ),
				),
				'features'  => array(
					'type'        => 'textarea',
					'label'       => __( 'Features', 'riaz-portfolio-core' ),
					'description' => __( 'One feature per line. The short description is the Excerpt.', 'riaz-portfolio-core' ),
				),
				'featured'  => array(
					'type'  => 'checkbox',
					'label' => __( 'Highlight this plan', 'riaz-portfolio-core' ),
				),
				'badge'     => array(
					'type'  => 'text',
					'label' => __( 'Badge (e.g. Most popular)', 'riaz-portfolio-core' ),
				),
				'btn_label' => array(
					'type'  => 'text',
					'label' => __( 'Button label', 'riaz-portfolio-core' ),
				),
				'btn_url'   => array(
					'type'  => 'url',
					'label' => __( 'Button link', 'riaz-portfolio-core' ),
				),
			),
		),
	);
}

/**
 * Register meta for REST/block editor access.
 */
function riaz_core_register_meta() {
	foreach ( riaz_core_meta_boxes() as $box ) {
		foreach ( $box['post_types'] as $post_type ) {
			foreach ( $box['fields'] as $key => $field ) {
				register_post_meta(
					$post_type,
					'_riaz_' . $key,
					array(
						'type'              => 'checkbox' === $field['type'] ? 'boolean' : 'string',
						'single'            => true,
						'show_in_rest'      => true,
						'sanitize_callback' => static function ( $value ) use ( $field ) {
							return riaz_core_sanitize_meta_value( $value, $field );
						},
						'auth_callback'     => static function ( $allowed, $meta_key, $post_id ) {
							return current_user_can( 'edit_post', $post_id );
						},
					)
				);
			}
		}
	}
}
add_action( 'init', 'riaz_core_register_meta', 20 );

/**
 * Add the meta boxes.
 */
function riaz_core_add_meta_boxes() {
	foreach ( riaz_core_meta_boxes() as $id => $box ) {
		add_meta_box(
			$id,
			$box['title'],
			'riaz_core_render_meta_box',
			$box['post_types'],
			$box['context'],
			isset( $box['priority'] ) ? $box['priority'] : 'high',
			array( 'box' => $id )
		);
	}
}
add_action( 'add_meta_boxes', 'riaz_core_add_meta_boxes' );

/**
 * Render a meta box.
 *
 * @param WP_Post $post Post.
 * @param array   $meta Callback args.
 */
function riaz_core_render_meta_box( $post, $meta ) {
	$boxes = riaz_core_meta_boxes();
	$box   = $boxes[ $meta['args']['box'] ];

	wp_nonce_field( 'riaz_core_save_' . $meta['args']['box'], 'riaz_core_nonce_' . $meta['args']['box'] );

	echo '<div class="riaz-meta-box">';
	foreach ( $box['fields'] as $key => $field ) {
		$name  = 'riaz_core_meta[' . $key . ']';
		$id    = 'riaz-core-meta-' . $key;
		$value = get_post_meta( $post->ID, '_riaz_' . $key, true );

		echo '<p class="riaz-meta-field">';
		if ( 'checkbox' === $field['type'] ) {
			printf(
				'<label for="%1$s"><input type="checkbox" id="%1$s" name="%2$s" value="1" %3$s> %4$s</label>',
				esc_attr( $id ),
				esc_attr( $name ),
				checked( (bool) $value, true, false ),
				esc_html( $field['label'] )
			);
		} else {
			printf( '<label for="%1$s"><strong>%2$s</strong></label><br>', esc_attr( $id ), esc_html( $field['label'] ) );
			switch ( $field['type'] ) {
				case 'textarea':
					printf( '<textarea id="%1$s" name="%2$s" rows="4" class="widefat">%3$s</textarea>', esc_attr( $id ), esc_attr( $name ), esc_textarea( $value ) );
					break;
				case 'select':
				case 'icon':
					$choices = 'icon' === $field['type'] ? array( '' => __( 'Default (sparkles)', 'riaz-portfolio-core' ) ) + riaz_core_icon_choices() : $field['choices'];
					printf( '<select id="%1$s" name="%2$s" class="widefat">', esc_attr( $id ), esc_attr( $name ) );
					foreach ( $choices as $choice => $label ) {
						printf( '<option value="%1$s" %2$s>%3$s</option>', esc_attr( $choice ), selected( (string) $value, (string) $choice, false ), esc_html( $label ) );
					}
					echo '</select>';
					break;
				case 'url':
					printf( '<input type="url" id="%1$s" name="%2$s" value="%3$s" class="widefat" placeholder="https://">', esc_attr( $id ), esc_attr( $name ), esc_attr( $value ) );
					break;
				case 'email':
					printf( '<input type="email" id="%1$s" name="%2$s" value="%3$s" class="widefat">', esc_attr( $id ), esc_attr( $name ), esc_attr( $value ) );
					break;
				default:
					printf( '<input type="text" id="%1$s" name="%2$s" value="%3$s" class="widefat">', esc_attr( $id ), esc_attr( $name ), esc_attr( $value ) );
			}
		}
		if ( ! empty( $field['description'] ) ) {
			printf( '<span class="description" style="display:block;margin-top:4px;">%s</span>', esc_html( $field['description'] ) );
		}
		echo '</p>';
	}
	echo '</div>';
}

/**
 * Sanitize a meta value according to its field type.
 *
 * @param mixed $value Raw value.
 * @param array $field Field definition.
 * @return mixed
 */
function riaz_core_sanitize_meta_value( $value, $field ) {
	switch ( $field['type'] ) {
		case 'checkbox':
			return (bool) $value;
		case 'url':
			return esc_url_raw( trim( (string) $value ) );
		case 'email':
			return sanitize_email( (string) $value );
		case 'textarea':
			return sanitize_textarea_field( (string) $value );
		case 'icon':
			$value = (string) $value;
			return array_key_exists( $value, riaz_core_icon_choices() ) ? $value : '';
		case 'select':
			$value = (string) $value;
			return array_key_exists( $value, $field['choices'] ) ? $value : '';
		default:
			return sanitize_text_field( (string) $value );
	}
}

/**
 * Save meta box values.
 *
 * @param int     $post_id Post ID.
 * @param WP_Post $post    Post.
 */
function riaz_core_save_meta_boxes( $post_id, $post ) {
	if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- verified per box below.
	$submitted = isset( $_POST['riaz_core_meta'] ) && is_array( $_POST['riaz_core_meta'] ) ? wp_unslash( $_POST['riaz_core_meta'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized per field.

	foreach ( riaz_core_meta_boxes() as $id => $box ) {
		if ( ! in_array( $post->post_type, $box['post_types'], true ) ) {
			continue;
		}
		$nonce_key = 'riaz_core_nonce_' . $id;
		if ( ! isset( $_POST[ $nonce_key ] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ $nonce_key ] ) ), 'riaz_core_save_' . $id ) ) {
			continue;
		}
		foreach ( $box['fields'] as $key => $field ) {
			$raw   = isset( $submitted[ $key ] ) ? $submitted[ $key ] : '';
			$value = riaz_core_sanitize_meta_value( $raw, $field );
			if ( '' === $value || false === $value ) {
				delete_post_meta( $post_id, '_riaz_' . $key );
			} else {
				update_post_meta( $post_id, '_riaz_' . $key, $value );
			}
		}
	}
}
add_action( 'save_post', 'riaz_core_save_meta_boxes', 10, 2 );
