<?php
/**
 * Portfolio post.
 *
 * @package     Supportte
 * @author      SVL Studios
 * @copyright   Copyright (c) 2021, SVL Studios
 * @link        https://www.svlstudios.com
 * @access      https://github.com/svl-studios/supportte
 * @since       Supportte 1.0.0
 */

get_header();

?>
	<div id="container">
		<div id="content" role="main">
			<div class="breadcrumbs">
				<?php the_title(); ?>
			</div>
			<?php

			global $wp_query;

			while ( have_posts() ) :
				the_post();

				?>
				<div class="portfolio_content">
					<div class="entry">
						<?php
						global $post;

						$portfolio_options = get_option( 'prtfl_options' );
						$meta_values       = get_post_custom( $post->ID );
						$post_thumbnail_id = get_post_thumbnail_id( $post->ID );
						if ( empty( $post_thumbnail_id ) ) {
							$args              = array(
								'post_parent'    => $post->ID,
								'post_type'      => 'attachment',
								'post_mime_type' => 'image',
								'orderby'        => 'menu_order',
								'order'          => 'ASC',
								'numberposts'    => 1,
							);
							$attachments       = get_children( $args );
							$post_thumbnail_id = key( $attachments );
						}

						$image       = wp_get_attachment_image_src( $post_thumbnail_id, 'portfolio-thumb' );
						$image_large = wp_get_attachment_image_src( $post_thumbnail_id, 'large' );
						$image_alt   = get_post_meta( $post_thumbnail_id, '_wp_attachment_image_alt', true );
						$image_desc  = get_post( $post_thumbnail_id );
						$image_desc  = $image_desc->post_content;

						if ( get_option( 'prtfl_postmeta_update' ) === '1' ) {
							$post_meta  = get_post_meta( $post->ID, 'prtfl_information', true );
							$date_compl = $post_meta['_prtfl_date_compl'];
							if ( ! empty( $date_compl ) && 'in progress' !== $date_compl ) {
								$date_compl = explode( '/', $date_compl );
								$date_compl = gmdate( get_option( 'date_format' ), strtotime( $date_compl[1] . '-' . $date_compl[0] . '-' . $date_compl[2] ) );
							}
							$the_link    = $post_meta['_prtfl_link'];
							$short_descr = $post_meta['_prtfl_short_descr'];
							$full_descr  = '' !== $post->post_content ? $post->post_content : $post_meta['_prtfl_descr'];
							$svn         = $post_meta['_prtfl_svn'];
						} else {
							$date_compl = get_post_meta( $post->ID, '_prtfl_date_compl', true );
							if ( ! empty( $date_compl ) && 'in progress' !== $date_compl ) {
								$date_compl = explode( '/', $date_compl );
								$date_compl = gmdate( get_option( 'date_format' ), strtotime( $date_compl[1] . '-' . $date_compl[0] . '-' . $date_compl[2] ) );
							}
							$the_link    = get_post_meta( $post->ID, '_prtfl_link', true );
							$short_descr = get_post_meta( $post->ID, '_prtfl_short_descr', true );
							$full_descr  = '' !== $post->post_content ? $post->post_content : get_post_meta( $post->ID, '_prtfl_descr', true );
							$svn         = get_post_meta( $post->ID, '_prtfl_svn', true );
						}

						?>
						<div class="portfolio_thumb">
							<a class="lightbox" rel="portfolio_fancybox" href="<?php echo esc_url( $image_large[0] ); ?>" title="<?php echo esc_attr( $image_desc ); ?>">
								<img src="<?php echo esc_url( $image[0] ); ?>" width="<?php echo esc_attr( $image[1] ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>"/>
							</a>
						</div>
						<div class="portfolio_short_content">
							<?php if ( true === (bool) $portfolio_options['prtfl_date_additional_field'] ) { ?>
								<p>
									<span class="lable">
										<?php echo wp_kses_post( $portfolio_options['prtfl_date_text_field'] ); ?>
									</span>
									<?php echo esc_html( $date_compl ); ?>
								</p>
								<?php
							}
							$user_id = get_current_user_id();
							if ( true === (bool) $portfolio_options['prtfl_link_additional_field'] ) {
								?>
								<?php if ( 0 === $user_id ) { ?>
									<p>
										<span class="lable"><?php echo wp_kses_post( $portfolio_options['prtfl_link_text_field'] ); ?></span>
										<?php echo esc_url( $the_link ); ?>
									</p>
								<?php } elseif ( false !== wp_parse_url( $the_link ) ) { ?>
									<p>
										<span class="lable"><?php echo wp_kses_post( $portfolio_options['prtfl_link_text_field'] ); ?></span>
										<a href="<?php echo esc_url( $the_link ); ?>"><?php echo esc_url( $the_link ); ?></a>
									</p>
								<?php } else { ?>
									<p><span class="lable"><?php echo wp_kses_post( $portfolio_options['prtfl_link_text_field'] ); ?></span>
										<?php echo esc_url( $the_link ); ?>
									</p>
								<?php } ?>
								<?php
							}
							if ( true === (bool) $portfolio_options['prtfl_description_additional_field'] ) {
								?>
								<p>
									<span class="lable"><?php echo wp_kses_post( $portfolio_options['prtfl_description_text_field'] ); ?></span>
									<?php echo wp_kses_post( str_replace( '\n', '<br />', $full_descr ) ); ?>
								</p>
							<?php } ?>
							<?php
							if ( 0 !== $user_id && $portfolio_options ) {
								if ( true === (bool) $portfolio_options['prtfl_svn_additional_field'] ) {
									?>
									<p>
										<span class="lable"><?php echo wp_kses_post( $portfolio_options['prtfl_svn_text_field'] ); ?></span>
										<?php echo wp_kses_post( $svn ); ?>
									</p>
									<?php
								}
								if ( true === (bool) $portfolio_options['prtfl_executor_additional_field'] ) {
									$executors_profile = wp_get_object_terms( $post->ID, 'portfolio_executor_profile' );
									?>
									<p>
										<span class="lable"><?php echo wp_kses_post( $portfolio_options['prtfl_executor_text_field'] ); ?></span>
										<?php
										$count = 0;
										foreach ( $executors_profile as $profile ) {
											if ( $count > 0 ) {
												$content .= ', ';
											}
											?>
											<a href="<?php echo esc_url( $profile->description ); ?>" title="<?php echo esc_attr( $profile->name ); ?> profile" target="_blank"><?php echo esc_html( $profile->name ); ?></a>
											<?php
											$count ++;
										}
										?>
									</p>
									<?php
								}
							}
							?>
						</div> <!-- .portfolio_short_content -->
						<div class="portfolio_images_block">
							<?php
							$args = array(
								'post_parent'    => $post->ID,
								'post_type'      => 'attachment',
								'post_mime_type' => 'image',
								'numberposts'    => - 1,
								'orderby'        => 'menu_order',
								'order'          => 'ASC',
								'exclude'        => $post_thumbnail_id,
							);

							$attachments             = get_children( $args );
							$array_post_thumbnail_id = array_keys( $attachments );
							$count_element           = count( $array_post_thumbnail_id );

							while ( list( $key, $value ) = each( $array_post_thumbnail_id ) ) {
								$image       = wp_get_attachment_image_src( $value, 'portfolio-photo-thumb' );
								$image_large = wp_get_attachment_image_src( $value, 'large' );
								$image_alt   = get_post_meta( $value, '_wp_attachment_image_alt', true );
								$image_title = get_post_meta( $value, '_wp_attachment_image_title', true );
								$image_desc  = get_post( $value );
								$image_desc  = $image_desc->post_content;

								if ( 0 === $key ) {
									?>
									<span class="lable"><?php echo wp_kses_post( $portfolio_options['prtfl_screenshot_text_field'] ); ?></span>
									<div class="portfolio_images_rows">
								<?php } ?>
										<div class="portfolio_images_gallery">
											<a class="lightbox" rel="portfolio_fancybox" href="<?php echo esc_url( $image_large[0] ); ?>" title="<?php echo esc_attr( $image_desc ); ?>">
												<img src="<?php echo esc_url( $image[0] ); ?>" width="<?php echo esc_attr( $image[1] ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>"/>
											</a>
											<br/>
											<?php echo esc_html( $image_title ); ?>
										</div>
								<?php if ( 0 === ( $key + 1 ) % $portfolio_options['prtfl_custom_image_row_count'] && 0 !== $key && $key + 1 !== $count_element ) { ?>
									</div> <!-- .portfolio_images_rows -->
									<div class="portfolio_images_rows">
									<?php
								}
							}
							if ( 0 < $count_element ) {
								?>
									</div><!-- .portfolio_images_rows -->
							<?php } ?>
						</div> <!-- .portfolio_images_block -->
					</div> <!-- .entry -->
					<div class="entry_footer">
						<div class="read_more">&nbsp;</div>
						<?php
						$terms = wp_get_object_terms( $post->ID, 'portfolio_technologies' );
						if ( is_array( $terms ) && count( $terms ) > 0 ) {
							?>
							<div class="portfolio_terms"><?php echo wp_kses_post( $portfolio_options['prtfl_technologies_text_field'] ); ?>
								<?php
								$count = 0;
								foreach ( $terms as $the_term ) {
									if ( $count > 0 ) {
										echo ', ';
									}

									// translators: %s = tern name.
									echo '<a href="' . esc_url( get_term_link( $the_term->slug, 'portfolio_technologies' ) ) . '" title="' . sprintf( esc_html__( 'View all posts in %s' ), esc_html( $the_term->name ) ) . '" >' . esc_html( $the_term->name ) . '</a>';
									$count ++;
								}
								?>
							</div>
						<?php } ?>
					</div> <!-- .entry_footer -->
				</div> <!-- .portfolio_content -->
			<?php endwhile; ?>
			<script type="text/javascript">
				(function( $ ) {
					$( document ).ready( function() {
						$( 'a[rel=portfolio_fancybox]' ).fancybox( {
							'transitionIn': 'elastic',
							'transitionOut': 'elastic',
							'titlePosition': 'inside',
							'speedIn': 500,
							'speedOut': 300,
							'titleFormat': function( title, currentArray, currentIndex, currentOpts ) {
								return '<span id="fancybox-title-inside">' + (title.length ? title + '<br />' : '') + 'Image ' + (currentIndex + 1) + ' / ' + currentArray.length + '</span>';
							}
						} );
					} );
				})( jQuery );
			</script>
		</div><!-- #content -->
	</div><!-- #container -->
	<input type="hidden" value="Version=2.08"/>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
