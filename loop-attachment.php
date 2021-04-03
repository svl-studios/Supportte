<?php
/**
 * The loop that displays an attachment.
 * The loop displays the posts and the post content. See
 * http://codex.wordpress.org/The_Loop to understand it and
 * http://codex.wordpress.org/Template_Tags to understand
 * the tags used in it.
 * This can be overridden in child themes with loop-attachment.php.
 *
 * @package     Supportte
 * @author      SVL Studios
 * @copyright   Copyright (c) 2021, SVL Studios
 * @link        https://www.svlstudios.com
 * @access      https://github.com/svl-studios/supportte
 * @since       Supportte 1.0.0
 */

if ( have_posts() ) {
	while ( have_posts() ) {

		the_post(); ?>

		<?php if ( ! empty( $post->post_parent ) ) { ?>
			<p class="page-title">
				<?php // translators: %s = page title. ?>
				<a href="<?php echo esc_url( get_permalink( $post->post_parent ) ); ?>" title="<?php echo esc_attr( sprintf( esc_html__( 'Return to %s', 'supportte' ), wp_strip_all_tags( get_the_title( $post->post_parent ) ) ) ); ?>" rel="gallery">
					<span class="meta-nav">&larr;</span>
					<?php get_the_title( $post->post_parent ); ?>
				</a>
			</p>
		<?php } ?>

		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<h2 class="entry-title"><?php the_title(); ?></h2>
			<div class="entry-meta">
				<span class="meta-prep meta-prep-author"><?php echo esc_html__( 'By', 'supportte' ); ?></span>
				<span class="author vcard">
					<?php // translators: %s = author name. ?>
					<a class="url fn n" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" title="<?php echo esc_attr( sprintf( esc_html__( 'View all posts by %s', 'supportte' ), get_the_author() ) ); ?>" rel="author">' . get_the_author() . '</a>
				</span>
				<span class="meta-sep">|</span>
				<span class="meta-prep meta-prep-entry-date">
					<?php echo esc_html__( 'Published', 'supportte' ); ?>
				</span>
				<span class="entry-date">
					<abbr class="published" title="<?php echo esc_attr( get_the_time() ); ?>"><?php echo get_the_date(); ?></abbr>
				</span>
				<?php
				if ( wp_attachment_is_image() ) {
					echo ' <span class="meta-sep">|</span> ';
					$metadata = wp_get_attachment_metadata();
					printf(
						// translators: %s = pixel size.
						esc_html__( 'Full size is %s pixels', 'supportte' ),
						'<a href="' . esc_url( wp_get_attachment_url() ) . '" title="' . esc_attr( esc_html__( 'Link to full-size image', 'supportte' ) ) . '">' . esc_html( $metadata['width'] ) . ' &times; ' . esc_html( $metadata['height'] ) . '</a>',
					);
				}
				?>
				<?php edit_post_link( __( 'Edit', 'supportte' ), '<span class="meta-sep">|</span> <span class="edit-link">', '</span>' ); ?>
			</div><!-- .entry-meta -->

			<div class="entry-content">
				<div class="entry-attachment">
					<?php
					if ( wp_attachment_is_image() ) {
						$attachments = array_values(
							get_children(
								array(
									'post_parent'    => $post->post_parent,
									'post_status'    => 'inherit',
									'post_type'      => 'attachment',
									'post_mime_type' => 'image',
									'order'          => 'ASC',
									'orderby'        => 'menu_order ID',
								)
							)
						);

						foreach ( $attachments as $k => $attachment ) {
							if ( $attachment->ID === $post->ID ) {
								break;
							}
						}

						$k ++;

						// If there is more than 1 image attachment in a gallery.
						if ( count( $attachments ) > 1 ) {
							if ( isset( $attachments[ $k ] ) ) { // get the URL of the next image attachment.
								$next_attachment_url = get_attachment_link( $attachments[ $k ]->ID );
							} else { // or get the URL of the first image attachment.
								$next_attachment_url = get_attachment_link( $attachments[0]->ID );
							}
						} else {
							// or, if there's only 1 image attachment, get the URL of the image.
							$next_attachment_url = wp_get_attachment_url();
						}

						?>
						<p class="attachment">
							<a href="<?php echo esc_url( $next_attachment_url ); ?>" title="<?php esc_attr( the_title_attribute() ); ?>" rel="attachment">
							<?php
							$attachment_width  = apply_filters( 'twentyten_attachment_size', 900 );
							$attachment_height = apply_filters( 'twentyten_attachment_height', 900 );

							echo wp_get_attachment_image(
								$post->ID,
								array(
									$attachment_width,
									$attachment_height,
								)
							); // filterable image width with, essentially, no limit for image height.

							?>
							</a>
						</p>
						<div id="nav-below" class="navigation">
							<div class="nav-previous"><?php previous_image_link( false ); ?></div>
							<div class="nav-next"><?php next_image_link( false ); ?></div>
						</div><!-- #nav-below -->
					<?php } else { ?>
						<a href="<?php echo esc_url( wp_get_attachment_url() ); ?>" title="<?php esc_attr( the_title_attribute() ); ?>" rel="attachment">
							<?php echo esc_html( basename( esc_url( get_permalink() ) ) ); ?>
						</a>
					<?php } ?>
				</div><!-- .entry-attachment -->
				<div class="entry-caption">
					<?php
					if ( ! empty( $post->post_excerpt ) ) {
						the_excerpt();
					}
					?>
				</div>

				<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'supportte' ) ); ?>
				<?php
				wp_link_pages(
					array(
						'before' => '<div class="page-link">' . esc_html__( 'Pages:', 'supportte' ),
						'after'  => '</div>',
					)
				);

				?>
			</div><!-- .entry-content -->
			<div class="entry-utility">
				<?php twentyten_posted_in(); ?>
				<?php edit_post_link( __( 'Edit', 'supportte' ), ' <span class="edit-link">', '</span>' ); ?>
			</div><!-- .entry-utility -->
		</div><!-- #post-## -->

		<?php comments_template(); ?>

		<?php
	} // end of the loop.
}
