<?php
/**
 * The template for displaying all pages.
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
		<?php

		/**
		 * Run the loop to output the page.
		 * If you want to overload this in a child theme then include a file
		 * called loop-page.php and that will be used instead.
		 */
		get_template_part( 'loop', 'page' );

		?>
	</div><!-- #content -->
</div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
