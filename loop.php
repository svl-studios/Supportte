<?php
/**
 * The loop that displays posts.
 * The loop displays the posts and the post content. See
 * http://codex.wordpress.org/The_Loop to understand it and
 * http://codex.wordpress.org/Template_Tags to understand
 * the tags used in it.
 * This can be overridden in child themes with loop.php or
 * loop-template.php, where 'template' is the loop context
 * requested by a template. For example, loop-index.php would
 * be used if it exists and we ask for the loop with:
 * <code>get_template_part( 'loop', 'index' );</code>
 *
 * @package     Supportte
 * @author      SVL Studios
 * @copyright   Copyright (c) 2021, SVL Studios
 * @link        https://www.svlstudios.com
 * @access      https://github.com/svl-studios/supportte
 * @since       Supportte 1.0.0
 */

?>
<?php /* Display navigation to next/previous pages when applicable */ ?>
<?php if ( $wp_query->max_num_pages > 1 ) { ?>
	<div id="nav-above" class="navigation">
		<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'supportte' ) ); ?></div>
		<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'supportte' ) ); ?></div>
	</div><!-- #nav-above -->
<?php } ?>

<?php /* If there are no posts to display, such as an empty archive page */ ?>
<?php if ( ! have_posts() ) { ?>
	<div id="post-0" class="post error404 not-found">
		<h1 class="entry-title"><?php esc_html_e( 'Not Found', 'supportte' ); ?></h1>
		<div class="entry-content">
			<p><?php esc_html_e( 'Apologies, but no results were found for the requested archive. Perhaps searching will help find a related post.', 'supportte' ); ?></p>
			<?php get_search_form(); ?>
		</div><!-- .entry-content -->
	</div><!-- #post-0 -->
<?php } ?>

<?php
while ( have_posts() ) {
	the_post();
	?>

	<?php /* How to display posts of the Gallery format. The gallery category is the old way. */ ?>
	<?php if ( ( function_exists( 'get_post_format' ) && 'gallery' === get_post_format( $post->ID ) ) || in_category( esc_html_x( 'gallery', 'gallery category slug', 'supportte' ) ) ) { ?>
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<h2 class="entry-title">
				<?php // translators: %s = title. ?>
				<a href="<?php esc_url( the_permalink() ); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'supportte' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
			</h2>

			<div class="entry-meta">
				<?php twentyten_posted_on(); ?>
			</div><!-- .entry-meta -->

			<div class="entry-content">
				<?php if ( post_password_required() ) { ?>
					<?php the_content(); ?>
				<?php } else { ?>
					<?php
					$images = get_children(
						array(
							'post_parent'    => $post->ID,
							'post_type'      => 'attachment',
							'post_mime_type' => 'image',
							'orderby'        => 'menu_order',
							'order'          => 'ASC',
							'posts_per_page' => 999,
						)
					);

					if ( $images ) {
						$total_images  = count( $images );
						$image         = array_shift( $images );
						$image_img_tag = wp_get_attachment_image( $image->ID, 'thumbnail' );
						?>
						<div class="gallery-thumb">
							<a class="size-thumbnail" href="<?php esc_url( the_permalink() ); ?>"><?php echo esc_html( $image_img_tag ); ?></a>
						</div><!-- .gallery-thumb -->
						<p>
							<em>
								<?php
								printf( _n( 'This gallery contains <a %1$s>%2$s photo</a>.', 'This gallery contains <a %1$s>%2$s photos</a>.', $total_images, 'supportte' ),
										'href="' . get_permalink() . '" title="' . sprintf( esc_attr__( 'Permalink to %s', 'supportte' ), the_title_attribute( 'echo=0' ) ) . '" rel="bookmark"',
										number_format_i18n( $total_images )
								);
								?>
							</em>
						</p>
					<?php } ?>
					<?php the_excerpt(); ?>
				<?php } ?>
			</div><!-- .entry-content -->

			<div class="entry-utility">
				<?php if ( function_exists( 'get_post_format' ) && 'gallery' === get_post_format( $post->ID ) ) { ?>
					<a href="<?php echo esc_url( get_post_format_link( 'gallery' ) ); ?>" title="<?php esc_attr_e( 'View Galleries', 'supportte' ); ?>"><?php esc_html_e( 'More Galleries', 'supportte' ); ?></a>
					<span class="meta-sep">|</span>
				<?php } elseif ( in_category( _x( 'gallery', 'gallery category slug', 'supportte' ) ) ) { ?>
					<a href="<?php echo esc_url( get_term_link( _x( 'gallery', 'gallery category slug', 'supportte' ), 'category' ) ); ?>" title="<?php esc_attr_e( 'View posts in the Gallery category', 'supportte' ); ?>"><?php esc_html_e( 'More Galleries', 'supportte' ); ?></a>
					<span class="meta-sep">|</span>
				<?php } ?>
				<span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'supportte' ), __( '1 Comment', 'supportte' ), __( '% Comments', 'supportte' ) ); ?></span>
				<?php edit_post_link( __( 'Edit', 'supportte' ), '<span class="meta-sep">|</span> <span class="edit-link">', '</span>' ); ?>
			</div><!-- .entry-utility -->
		</div><!-- #post-## -->

		<?php /* How to display posts of the Aside format. The asides category is the old way. */ ?>

	<?php } elseif ( ( function_exists( 'get_post_format' ) && 'aside' === get_post_format( $post->ID ) ) || in_category( _x( 'asides', 'asides category slug', 'supportte' ) ) ) { ?>
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<?php if ( is_archive() || is_search() ) { // Display excerpts for archives and search. ?>
				<div class="entry-summary">
					<?php the_excerpt(); ?>
				</div><!-- .entry-summary -->
			<?php } else { ?>
				<div class="entry-content">
					<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'supportte' ) ); ?>
				</div><!-- .entry-content -->
			<?php } ?>
			<div class="entry-utility">
				<?php twentyten_posted_on(); ?>
				<span class="meta-sep">|</span>
				<span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'supportte' ), __( '1 Comment', 'supportte' ), __( '% Comments', 'supportte' ) ); ?></span>
				<?php edit_post_link( __( 'Edit', 'supportte' ), '<span class="meta-sep">|</span> <span class="edit-link">', '</span>' ); ?>
			</div><!-- .entry-utility -->
		</div><!-- #post-## -->

		<?php /* How to display all other posts. */ ?>

	<?php } else { ?>
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<h2 class="entry-title">
				<?php // translators: %s = permalink. ?>
				<a href="<?php esc_url( the_permalink() ); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'supportte' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
			</h2>
			<div class="entry-meta">
				<?php twentyten_posted_on(); ?>
			</div><!-- .entry-meta -->

			<?php if ( is_archive() || is_search() ) { // Only display excerpts for archives and search. ?>
				<div class="entry-summary">
					<?php the_excerpt(); ?>
				</div><!-- .entry-summary -->
			<?php } else { ?>
				<div class="entry-content">
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
			<?php } ?>
			<div class="entry-utility">
				<?php if ( count( get_the_category() ) ) { ?>
					<span class="cat-links">
						<?php echo '<span class="entry-utility-prep entry-utility-prep-cat-links">' . esc_html__( 'Posted in', 'supportte' ) . '</span> ' . wp_kses_post( get_the_category_list( ', ' ) ); ?>
					</span>
					<span class="meta-sep">|</span>
				<?php } ?>
				<?php
				$tags_list = get_the_tag_list( '', ', ' );
				if ( $tags_list ) {
					?>
					<span class="tag-links">
						<?php echo '<span class="entry-utility-prep entry-utility-prep-tag-links">' . esc_html__( 'Tagged', 'supportte' ) . '</span> ' . wp_kses_post( $tags_list ); ?>
					</span>
					<span class="meta-sep">|</span>
				<?php } ?>
				<span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'supportte' ), __( '1 Comment', 'supportte' ), __( '% Comments', 'supportte' ) ); ?></span>
				<?php edit_post_link( esc_html__( 'Edit', 'supportte' ), '<span class="meta-sep">|</span> <span class="edit-link">', '</span>' ); ?>
			</div><!-- .entry-utility -->
		</div><!-- #post-## -->

		<?php comments_template( '', true ); ?>
	<?php } // This was the if statement that broke the loop into three parts based on categories. ?>
<?php } // End the loop. Whew. ?>

<?php /* Display navigation to next/previous pages when applicable */ ?>
<?php if ( $wp_query->max_num_pages > 1 ) { ?>
	<div id="nav-below" class="navigation">
		<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'supportte' ) ); ?></div>
		<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'supportte' ) ); ?></div>
	</div><!-- #nav-below -->
<?php } ?>
