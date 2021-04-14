<?php
/**
 * Template Name: Search Forum.
 *
 * @package     Supportte
 * @author      SVL Studios
 * @copyright   Copyright (c) 2021, SVL Studios
 * @link        https://www.svlstudios.com
 * @access      https://github.com/svl-studios/supportte
 * @since       Supportte 1.0.0
 */

get_header();

$http_get = ( 'GET' === $_SERVER['REQUEST_METHOD'] ?? '' );

if ( isset( $_GET['_wp_nonce'] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_wp_nonce'] ) ), 'supportte_search' ) ) {
	$the_search = $http_get ? sanitize_text_field( wp_unslash( $_GET['q'] ?? '' ) ) : '';
	$args       = array(
		's' => $the_search,
	);
} else {
	wp_nonce_ays( 'expired' );
}

?>
<div id="container">
	<div id="content" role="main">
		<div id="forums-search">
			<form role="search" method="get" id="searchform" class="searchform" action="<?php echo esc_url( site_url( '/search' ) ); ?>">
				<?php wp_nonce_field( 'supportte_search' ); ?>
				<input type="text" value="<?php echo esc_attr( $the_search ); ?>" name="q" class="search" placeholder="<?php esc_attr__( 'Search the forums...', 'supportte' ); ?>">
				<input type="submit" class="searchsubmit" value="<?php esc_attr__( 'Search', 'supportte' ); ?>">
			</form>
		</div>
		<br/>
		<hr/>
		<?php
		if ( have_posts() ) {
			while ( have_posts() ) {
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
		?>
	</div><!-- #content -->
</div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
