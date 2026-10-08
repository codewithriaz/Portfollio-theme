<?php
/**
 * Comments.
 *
 * @package Riaz_Portfolio
 */

defined( 'ABSPATH' ) || exit;

if ( post_password_required() ) {
	return;
}
?>
<section id="comments" class="comments-area">
	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title">
			<?php
			$riaz_count = get_comments_number();
			printf(
				/* translators: 1: comment count, 2: post title. */
				esc_html( _n( '%1$s comment on “%2$s”', '%1$s comments on “%2$s”', $riaz_count, 'riaz-portfolio' ) ),
				esc_html( number_format_i18n( $riaz_count ) ),
				esc_html( get_the_title() )
			);
			?>
		</h2>
		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'callback'    => 'riaz_comment',
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 40,
				)
			);
			?>
		</ol>
		<?php the_comments_navigation(); ?>
		<?php if ( ! comments_open() ) : ?>
			<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'riaz-portfolio' ); ?></p>
		<?php endif; ?>
	<?php endif; ?>

	<?php
	comment_form(
		array(
			'class_submit'       => 'submit',
			'title_reply_before' => '<h2 id="reply-title" class="comment-reply-title">',
			'title_reply_after'  => '</h2>',
		)
	);
	?>
</section>
