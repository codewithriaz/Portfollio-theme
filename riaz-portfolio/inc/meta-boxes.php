<?php
/**
 * Per-page display options (banner and header style).
 *
 * Content-type fields (services, projects, testimonials…) live in the
 * Riaz Portfolio Core plugin. Fields here are declared once and used for
 * rendering, saving (nonce + capability checks + sanitization) and REST.
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * Meta box definitions keyed by box ID.
 *
 * @return array
 */
function riaz_meta_boxes() {
	$display_types = array( 'page', 'post', 'riaz_portfolio', 'riaz_case_study', 'riaz_service' );

	return array(
		'riaz_display' => array(
			'title'      => __( 'Display options', 'riaz-portfolio' ),
			'post_types' => $display_types,
			'context'    => 'side',
			'priority'   => 'low',
			'fields'     => array(
				'hide_banner'     => array(
					'type'  => 'checkbox',
					'label' => __( 'Hide the page banner (title area)', 'riaz-portfolio' ),
				),
				'banner_subtitle' => array(
					'type'  => 'textarea',
					'label' => __( 'Banner subtitle', 'riaz-portfolio' ),
				),
				'header_style'    => array(
					'type'        => 'select',
					'label'       => __( 'Header style', 'riaz-portfolio' ),
					'description' => __( '"Transparent" suits pages that start with a dark section.', 'riaz-portfolio' ),
					'choices'     => array(
						''        => __( 'Automatic', 'riaz-portfolio' ),
						'overlay' => __( 'Transparent', 'riaz-portfolio' ),
						'solid'   => __( 'Solid', 'riaz-portfolio' ),
					),
				),
			),
		),
	);
}

/**
 * Register meta for REST/block editor access.
 */
function riaz_register_meta() {
	foreach ( riaz_meta_boxes() as $box ) {
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
							return riaz_sanitize_meta_value( $value, $field );
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
add_action( 'init', 'riaz_register_meta', 20 );

/**
 * Add the meta boxes.
 */
function riaz_add_meta_boxes() {
	foreach ( riaz_meta_boxes() as $id => $box ) {
		add_meta_box(
			$id,
			$box['title'],
			'riaz_render_meta_box',
			$box['post_types'],
			$box['context'],
			isset( $box['priority'] ) ? $box['priority'] : 'high',
			array( 'box' => $id )
		);
	}
}
add_action( 'add_meta_boxes', 'riaz_add_meta_boxes' );

/**
 * Render a meta box.
 *
 * @param WP_Post $post Post.
 * @param array   $meta Callback args.
 */
function riaz_render_meta_box( $post, $meta ) {
	$boxes = riaz_meta_boxes();
	$box   = $boxes[ $meta['args']['box'] ];

	wp_nonce_field( 'riaz_save_' . $meta['args']['box'], 'riaz_nonce_' . $meta['args']['box'] );

	echo '<div class="riaz-meta-box">';
	foreach ( $box['fields'] as $key => $field ) {
		$name  = 'riaz_meta[' . $key . ']';
		$id    = 'riaz-meta-' . $key;
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
					$choices = 'icon' === $field['type'] ? array( '' => __( 'Default (sparkles)', 'riaz-portfolio' ) ) + riaz_icon_choices() : $field['choices'];
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
function riaz_sanitize_meta_value( $value, $field ) {
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
			return array_key_exists( $value, riaz_icon_choices() ) ? $value : '';
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
function riaz_save_meta_boxes( $post_id, $post ) {
	if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- verified per box below.
	$submitted = isset( $_POST['riaz_meta'] ) && is_array( $_POST['riaz_meta'] ) ? wp_unslash( $_POST['riaz_meta'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized per field.

	foreach ( riaz_meta_boxes() as $id => $box ) {
		if ( ! in_array( $post->post_type, $box['post_types'], true ) ) {
			continue;
		}
		$nonce_key = 'riaz_nonce_' . $id;
		if ( ! isset( $_POST[ $nonce_key ] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ $nonce_key ] ) ), 'riaz_save_' . $id ) ) {
			continue;
		}
		foreach ( $box['fields'] as $key => $field ) {
			$raw   = isset( $submitted[ $key ] ) ? $submitted[ $key ] : '';
			$value = riaz_sanitize_meta_value( $raw, $field );
			if ( '' === $value || false === $value ) {
				delete_post_meta( $post_id, '_riaz_' . $key );
			} else {
				update_post_meta( $post_id, '_riaz_' . $key, $value );
			}
		}
	}
}
add_action( 'save_post', 'riaz_save_meta_boxes', 10, 2 );
