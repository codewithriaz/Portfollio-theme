<?php
/**
 * Built-in contact form.
 *
 * Submissions go to admin-post.php and are protected by a nonce, a honeypot
 * field, a minimum fill time and a per-visitor rate limit. Messages are sent
 * with wp_mail() and nothing is stored in the database.
 *
 * If your site uses full-page caching, either exclude the contact page from
 * the cache or use a form plugin (Customizer → Contact Details), because
 * cached nonces expire after 24 hours.
 *
 * Use it anywhere with the [riaz_contact_form] shortcode.
 *
 * @package Riaz_Portfolio_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Status messages shown after a submission.
 *
 * @return array<string,array{0:string,1:string}> code => [ type, message ].
 */
function riaz_core_contact_messages() {
	return array(
		'sent'    => array( 'success', __( 'Thank you! Your message has been sent. I will get back to you soon.', 'riaz-portfolio-core' ) ),
		'invalid' => array( 'error', __( 'Please fill in your name, a valid email address and a message.', 'riaz-portfolio-core' ) ),
		'expired' => array( 'error', __( 'The form expired. Please refresh the page and try again.', 'riaz-portfolio-core' ) ),
		'limit'   => array( 'error', __( 'Too many messages in a short time. Please try again in a few minutes.', 'riaz-portfolio-core' ) ),
		'error'   => array( 'error', __( 'Sorry, your message could not be sent. Please email me directly instead.', 'riaz-portfolio-core' ) ),
	);
}

/**
 * Render the form.
 *
 * @param array $args Optional: 'note' (text under the form), 'button' (label).
 */
function riaz_core_contact_form( $args = array() ) {
	$args     = wp_parse_args(
		$args,
		array(
			'note'   => __( 'Your details are only used to reply to your message.', 'riaz-portfolio-core' ),
			'button' => __( 'Send message', 'riaz-portfolio-core' ),
		)
	);
	$uid      = wp_unique_id( 'rp-' );
	$messages = riaz_core_contact_messages();
	$status   = isset( $_GET['riaz_contact'] ) ? sanitize_key( wp_unslash( $_GET['riaz_contact'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display only.
	?>
	<form id="riaz-contact-form" class="rp-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<?php if ( isset( $messages[ $status ] ) ) : ?>
			<p class="rp-notice is-<?php echo esc_attr( $messages[ $status ][0] ); ?>" role="<?php echo 'success' === $messages[ $status ][0] ? 'status' : 'alert'; ?>"><?php echo esc_html( $messages[ $status ][1] ); ?></p>
		<?php endif; ?>

		<input type="hidden" name="action" value="riaz_contact">
		<input type="hidden" name="riaz_ts" value="<?php echo esc_attr( (string) time() ); ?>">
		<?php wp_nonce_field( 'riaz_contact', 'riaz_contact_nonce' ); ?>

		<div class="rp-field">
			<label for="<?php echo esc_attr( $uid ); ?>-name"><?php esc_html_e( 'Your name', 'riaz-portfolio-core' ); ?> <span class="required" aria-hidden="true">*</span></label>
			<input id="<?php echo esc_attr( $uid ); ?>-name" type="text" name="riaz_name" required maxlength="100" autocomplete="name">
		</div>
		<div class="rp-field">
			<label for="<?php echo esc_attr( $uid ); ?>-email"><?php esc_html_e( 'Email address', 'riaz-portfolio-core' ); ?> <span class="required" aria-hidden="true">*</span></label>
			<input id="<?php echo esc_attr( $uid ); ?>-email" type="email" name="riaz_email" required maxlength="200" autocomplete="email">
		</div>
		<div class="rp-field is-full">
			<label for="<?php echo esc_attr( $uid ); ?>-company"><?php esc_html_e( 'Company or website', 'riaz-portfolio-core' ); ?></label>
			<input id="<?php echo esc_attr( $uid ); ?>-company" type="text" name="riaz_company" maxlength="200" autocomplete="organization">
		</div>
		<div class="rp-field is-full">
			<label for="<?php echo esc_attr( $uid ); ?>-message"><?php esc_html_e( 'How can I help?', 'riaz-portfolio-core' ); ?> <span class="required" aria-hidden="true">*</span></label>
			<textarea id="<?php echo esc_attr( $uid ); ?>-message" name="riaz_message" required minlength="10" maxlength="5000" rows="6"></textarea>
		</div>
		<div class="rp-hp" aria-hidden="true">
			<label for="<?php echo esc_attr( $uid ); ?>-website"><?php esc_html_e( 'Leave this field empty', 'riaz-portfolio-core' ); ?></label>
			<input id="<?php echo esc_attr( $uid ); ?>-website" type="text" name="riaz_website" tabindex="-1" autocomplete="off">
		</div>
		<div class="rp-form-actions">
			<?php if ( $args['note'] ) : ?>
				<p class="rp-form-note"><?php echo esc_html( $args['note'] ); ?></p>
			<?php endif; ?>
			<button type="submit" class="btn">
				<?php echo esc_html( $args['button'] ); ?>
				<span class="btn-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" focusable="false"><path d="M14.536 21.686a.5.5 0 0 0 .937-.024l6.5-19a.496.496 0 0 0-.635-.635l-19 6.5a.5.5 0 0 0-.024.937l7.93 3.18a2 2 0 0 1 1.112 1.11z"/><path d="m21.854 2.147-10.94 10.939"/></svg></span>
			</button>
		</div>
	</form>
	<?php
}

/**
 * Handle a submission.
 */
function riaz_core_handle_contact() {
	$redirect = wp_get_referer();
	$redirect = $redirect ? remove_query_arg( 'riaz_contact', $redirect ) : home_url( '/' );
	$finish   = static function ( $code ) use ( $redirect ) {
		wp_safe_redirect( add_query_arg( 'riaz_contact', $code, $redirect ) . '#riaz-contact-form' );
		exit;
	};

	if ( ! isset( $_POST['riaz_contact_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['riaz_contact_nonce'] ) ), 'riaz_contact' ) ) {
		$finish( 'expired' );
	}

	// Bots: a filled honeypot or an instant submit is quietly accepted and dropped.
	$started = isset( $_POST['riaz_ts'] ) ? absint( $_POST['riaz_ts'] ) : 0;
	if ( ! empty( $_POST['riaz_website'] ) || ( time() - $started ) < 3 ) {
		$finish( 'sent' );
	}

	$name    = isset( $_POST['riaz_name'] ) ? sanitize_text_field( wp_unslash( $_POST['riaz_name'] ) ) : '';
	$email   = isset( $_POST['riaz_email'] ) ? sanitize_email( wp_unslash( $_POST['riaz_email'] ) ) : '';
	$company = isset( $_POST['riaz_company'] ) ? sanitize_text_field( wp_unslash( $_POST['riaz_company'] ) ) : '';
	$message = isset( $_POST['riaz_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['riaz_message'] ) ) : '';

	$name    = mb_substr( $name, 0, 100 );
	$company = mb_substr( $company, 0, 200 );
	$message = mb_substr( $message, 0, 5000 );

	if ( '' === $name || ! is_email( $email ) || mb_strlen( $message ) < 10 ) {
		$finish( 'invalid' );
	}

	$ip    = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$key   = 'riaz_contact_' . md5( $ip . wp_salt( 'nonce' ) );
	$count = (int) get_transient( $key );
	if ( $count >= 3 ) {
		$finish( 'limit' );
	}
	set_transient( $key, $count + 1, 10 * MINUTE_IN_SECONDS );

	/**
	 * Recipient of contact form messages (the theme supplies its contact email).
	 *
	 * @param string $to Email address.
	 */
	$to = sanitize_email( (string) apply_filters( 'riaz_core_contact_recipient', get_option( 'admin_email' ) ) );
	$to = is_email( $to ) ? $to : get_option( 'admin_email' );

	// The referer is usually root-relative; make it a full URL for the email.
	$page = $redirect;
	if ( 0 === strpos( $page, '/' ) ) {
		$home = wp_parse_url( home_url() );
		$page = $home['scheme'] . '://' . $home['host'] . ( isset( $home['port'] ) ? ':' . $home['port'] : '' ) . $page;
	}

	$site      = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
	$safe_name = trim( preg_replace( '/[^\p{L}\p{N} .\'-]/u', '', $name ) );
	/* translators: 1: site name, 2: sender name. */
	$subject = sprintf( __( '[%1$s] New message from %2$s', 'riaz-portfolio-core' ), $site, $safe_name );
	$body    = sprintf(
		"%s: %s\n%s: %s\n%s: %s\n\n%s\n\n—\n%s",
		__( 'Name', 'riaz-portfolio-core' ),
		$name,
		__( 'Email', 'riaz-portfolio-core' ),
		$email,
		__( 'Company', 'riaz-portfolio-core' ),
		$company ? $company : '—',
		$message,
		/* translators: %s: page URL. */
		sprintf( __( 'Sent from %s', 'riaz-portfolio-core' ), $page )
	);
	$headers = array( sprintf( 'Reply-To: %s <%s>', $safe_name, $email ) );

	$sent = wp_mail( $to, $subject, $body, $headers );

	/**
	 * Fires after a contact form submission was processed.
	 *
	 * @param bool  $sent Whether wp_mail() reported success.
	 * @param array $data Submitted, sanitized fields.
	 */
	do_action( 'riaz_core_contact_submitted', $sent, compact( 'name', 'email', 'company', 'message' ) );

	$finish( $sent ? 'sent' : 'error' );
}
add_action( 'admin_post_nopriv_riaz_contact', 'riaz_core_handle_contact' );
add_action( 'admin_post_riaz_contact', 'riaz_core_handle_contact' );
