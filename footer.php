<?php
/**
 * The template for displaying the footer.
 * Contains the closing of the id=main div and all content
 * after. Calls sidebar-footer.php for bottom widgets.
 *
 * @package     Supportte
 * @author      SVL Studios
 * @copyright   Copyright (c) 2021, SVL Studios
 * @link        https://www.svlstudios.com
 * @access      https://github.com/svl-studios/supportte
 * @since       Supportte 1.0.0
 */

?>
</div><!-- #main -->

<div id="footer" role="contentinfo">
	<div id="colophon">

		<?php
		/**
		 * A sidebar in the footer? Yep. You can can customize
		 * your footer with four columns of widgets.
		 */
		get_sidebar( 'footer' );

		?>
		<div id="site-info">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
				<?php bloginfo( 'name' ); ?>
			</a>
		</div><!-- #site-info -->
	</div><!-- #colophon -->
</div><!-- #footer -->

</div><!-- #wrapper -->

<?php

wp_footer();

?>
</body>
</html>
