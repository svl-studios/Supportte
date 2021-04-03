<?php
/**
 * The template for displaying 404 pages (Not Found).
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
			<div id="post-0" class="post error404 not-found">
				<h1 class="entry-title"><?php esc_html_e( 'Not Found', 'supportte' ); ?></h1>
				<div class="entry-content">
					<p><?php esc_html_e( 'Apologies, but the page you requested could not be found. Perhaps searching will help.', 'supportte' ); ?></p>
					<?php get_search_form(); ?>
				</div><!-- .entry-content -->
			</div><!-- #post-0 -->
		</div><!-- #content -->
	</div><!-- #container -->
	<script type="text/javascript">
		// focus on search field after it has loaded
		document.getElementById( 's' ) && document.getElementById( 's' ).focus();
	</script>
<?php

get_footer();
