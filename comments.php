<?php
/**
 * The template for displaying Comments.
 * The area of the page that contains both current comments
 * and the comment form. The actual display of comments is
 * handled by a callback to twentyten_comment which is
 * located in the functions.php file.
 *
 * @package     Supportte
 * @author      SVL Studios
 * @copyright   Copyright (c) 2021, SVL Studios
 * @link        https://www.svlstudios.com
 * @access      https://github.com/svl-studios/supportte
 * @since       Supportte 1.0.0
 */

?>
<div id="comments">
	<?php if ( post_password_required() ) { ?>
		<p class="nopassword"><?php esc_html_e( 'This post is password protected. Enter the password to view any comments.', 'supportte' ); ?></p>
	</div><!-- #comments -->
		<?php

		/**
		 * Stop the rest of comments.php from being processed,
		 * but don't kill the script entirely -- we still have
		 * to fully load the template.
		 */

		return;
	}

	if ( have_comments() ) {
		?>
		<h3 id="comments-title">
			<?php
			printf( _n( 'One Response to %2$s', '%1$s Responses to %2$s', get_comments_number(), 'supportte' ),
				number_format_i18n( get_comments_number() ), '<em>' . get_the_title() . '</em>' );
			?>
		</h3>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) { // Are there comments to navigate through? ?>
			<div class="navigation">
				<div class="nav-previous"><?php previous_comments_link( __( '<span class="meta-nav">&larr;</span> Older Comments', 'supportte' ) ); ?></div>
				<div class="nav-next"><?php next_comments_link( __( 'Newer Comments <span class="meta-nav">&rarr;</span>', 'supportte' ) ); ?></div>
			</div> <!-- .navigation -->
		<?php } // check for comment navigation ?>

		<ol class="commentlist">
			<?php

			/**
			 * Loop through and list the comments. Tell wp_list_comments()
			 * to use twentyten_comment() to format the comments.
			 * If you want to overload this in a child theme then you can
			 * define twentyten_comment() and that will be used instead.
			 * See twentyten_comment() in twentyten/functions.php for more.
			 */
			wp_list_comments( array( 'callback' => 'twentyten_comment' ) );
			?>
		</ol>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) { // Are there comments to navigate through? ?>
			<div class="navigation">
				<div class="nav-previous"><?php previous_comments_link( __( '<span class="meta-nav">&larr;</span> Older Comments', 'supportte' ) ); ?></div>
				<div class="nav-next"><?php next_comments_link( __( 'Newer Comments <span class="meta-nav">&rarr;</span>', 'supportte' ) ); ?></div>
			</div><!-- .navigation -->
		<?php } // check for comment navigation ?>
		<?php
	} else { // or, if we don't have comments.

		/**
		 * If there are no comments and comments are closed,
		 * let's leave a little note, shall we?
		 */
		if ( ! comments_open() ) {
			?>
			<p class="nocomments"><?php esc_html_e( 'Comments are closed.', 'supportte' ); ?></p>
			<?php
		}
		?>
<?php } // end have_comments() ?>

<?php comment_form(); ?>

</div><!-- #comments -->
