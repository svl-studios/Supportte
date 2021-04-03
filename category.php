<?php
/**
 * The template for displaying Category Archive pages.
 *
 * @package     Supportte
 * @author      SVL Studios
 * @copyright   Copyright (c) 2021, SVL Studios
 * @link        https://www.svlstudios.com
 * @access      https://github.com/svl-studios/supportte
 * @since       Supportte 1.0.0
 */

get_header(); ?>

<div id="container">
	<div id="content" role="main">

		<h1 class="page-title">
			<?php // translators: %s = category title. ?>
			<?php printf( esc_html__( 'Category Archives: %s', 'supportte' ), '<span>' . single_cat_title( '', false ) . '</span>' ); ?>
		</h1>
		<?php

		$category_description = category_description();
		if ( ! empty( $category_description ) ) {
			echo '<div class="archive-meta">' . esc_html( $category_description ) . '</div>';
		}

		/**
		 * Run the loop for the category page to output the posts.
		 * If you want to overload this in a child theme then include a file
		 * called loop-category.php and that will be used instead.
		 */
		get_template_part( 'loop', 'category' );

		?>
	</div><!-- #content -->
</div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
