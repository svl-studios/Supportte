<?php
/**
 * The template for displaying Search Results pages.
 *
 * @package     Supportte
 * @author      SVL Studios
 * @copyright   Copyright (c) 2021, SVL Studios
 * @link        https://www.svlstudios.com
 * @access      https://github.com/svl-studios/supportte
 * @since       Supportte 1.0.0
 */

/**
 * Display forum search results
 */
get_header();

$http_get = ( 'GET' === $_SERVER['REQUEST_METHOD'] ?? '' );

if ( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_wpnonce'] ) ), 'supportte_search' ) ) {
	$the_search = $http_get ? sanitize_text_field( wp_unslash( $_GET['s'] ?? '' ) ) : '';
	$args       = array(
		's' => $the_search,
	);
} else {
	wp_nonce_ays( 'expired' );
}

?>
<div id="container">
	<div id="content" role="main">
		<?php if ( $the_search ) { ?>
			<div id="forums-search">
				<form role="search" method="get" id="searchform" class="searchform" action="<?php echo esc_url( site_url( '/' ) ); ?>">
					<?php wp_nonce_field( 'supportte_search', '_wpnonce', false ); ?>
					<input type="text" value="<?php echo esc_attr( $the_search ); ?>" name="s" class="search" placeholder="<?php esc_html__( 'Search the forums...', 'supportte' ); ?>">
					<input type="submit" class="searchsubmit" value="Search">
				</form>
			</div>
			<br/>
			<hr/>
			<?php
			global $wp_query;

			if ( have_posts() ) {
				while ( have_posts() ) {
					var_dump('loop');
					the_post();
					?>
					<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						<div class="entry-content">
							<div id="bbpress-forums">
								<?php if ( bbp_has_topics( $args ) ) { ?>
									<?php bbp_get_template_part( 'loop', 'topics' ); ?>
									<?php bbp_get_template_part( 'pagination', 'topics' ); ?>
								<?php } else { ?>
									<?php esc_html__( 'Sorry, no results found for', 'supportte' ); ?> <strong><?php echo esc_html( $the_search ); ?></strong>.
								<?php } ?>
							</div>
						</div><!-- .entry-content -->
					</div><!-- #post-## -->
					<?php
				}
			} // end of the loop.
		} else {
			if ( have_posts() ) {
				?>
				<h1 class="page-title">
					<?php // translators: %s = search query. ?>
					<?php printf( esc_html__( 'Search Results for: %s', 'supportte' ), '<span>' . get_search_query() . '</span>' ); ?>
				</h1>
				<?php

				/**
				 * Run the loop for the search to output the results.
				 * If you want to overload this in a child theme then include a file
				 * called loop-search.php and that will be used instead.
				 */
				get_template_part( 'loop', 'search' );
				?>
			<?php } else { ?>
				<div id="post-0" class="post no-results not-found">
					<h2 class="entry-title"><?php esc_html_e( 'Nothing Found', 'supportte' ); ?></h2>
					<div class="entry-content">
						<p><?php esc_html_e( 'Sorry, but nothing matched your search criteria. Please try again with some different keywords.', 'supportte' ); ?></p>
						<?php get_search_form(); ?>
					</div><!-- .entry-content -->
				</div><!-- #post-0 -->
			<?php } ?>
		<?php } ?>
	</div><!-- #content -->
</div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
