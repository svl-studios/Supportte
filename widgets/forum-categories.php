<?php
/**
 * Form categopry widget.
 *
 * @package     Supportte
 * @author      SVL Studios
 * @copyright   Copyright (c) 2021, SVL Studios
 * @link        https://www.svlstudios.com
 * @access      https://github.com/svl-studios/supportte
 * @since       Supportte 1.0.0
 */

/**
 * Plugin: bbPress Forum Widget
 * Adds a widget which displays the forum list
 *
 * @since bbPress (r2653)
 * @uses  WP_Widget
 */
add_action( 'bbp_widgets_init', array( 'AQ_Forums_Widget', 'register_widget' ), 10 );

/**
 * Class AQ_Forums_Widget
 */
class AQ_Forums_Widget extends WP_Widget {

	/**
	 * BbPress Forum Widget
	 * Registers the forum widget
	 *
	 * @since bbPress (r2653)
	 * @uses  apply_filters() Calls 'bbp_forums_widget_options' with the
	 *                        widget options
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'   => 'aq_widget_display_forums',
			'description' => __( 'A list of forums with an option to set the parent.', 'supportte' ),
		);

		parent::__construct( false, __( 'Supportte Forums List', 'supportte' ), $widget_ops );
	}

	/**
	 * Register the widget
	 *
	 * @since bbPress (r3389)
	 * @uses  register_widget()
	 */
	public static function register_widget() {
		register_widget( 'AQ_Forums_Widget' );
	}

	/**
	 * Displays the output, the forum list
	 *
	 * @param mixed $args     Arguments.
	 * @param mixed $instance Instance.
	 *
	 * @since bbPress (r2653)
	 * @uses  apply_filters() Calls 'bbp_forum_widget_title' with the title
	 * @uses  get_option() To get the forums per page option
	 * @uses  current_user_can() To check if the current user can read
	 *                           private() To resety name
	 * @uses  bbp_has_forums() The main forum loop
	 * @uses  bbp_forums() To check whether there are more forums available
	 *                     in the loop
	 * @uses  bbp_the_forum() Loads up the current forum in the loop
	 * @uses  bbp_forum_permalink() To display the forum permalink
	 * @uses  bbp_forum_title() To display the forum title
	 */
	public function widget( $args, $instance ) {

		// phpcs:ignore WordPress.PHP.DontExtract
		extract( $args );

		$title        = apply_filters( 'bbp_forum_widget_title', $instance['title'] );
		$parent_forum = ! empty( $instance['parent_forum'] ) ? $instance['parent_forum'] : '0';

		// Note: private and hidden forums will be excluded via the
		// bbp_pre_get_posts_exclude_forums filter and function.
		$widget_query = new WP_Query(
			array(
				'post_parent'    => $parent_forum,
				'post_type'      => bbp_get_forum_post_type(),
				'posts_per_page' => get_option( '_bbp_forums_per_page', 50 ),
				'orderby'        => 'menu_order',
				'order'          => 'ASC',
			)
		);

		if ( $widget_query->have_posts() ) {
			echo wp_kses_post( $before_widget );
			echo wp_kses_post( $before_title . $title . $after_title );

			$current_forum_id = bbp_get_forum_id();

			?>
			<ul>
				<?php
				while ( $widget_query->have_posts() ) {
					$widget_query->the_post();
					?>
					<?php $current = $widget_query->post->ID === $current_forum_id ? 'current' : ''; ?>
					<li>
						<a class="bbp-forum-title <?php echo esc_html( $current ); ?>" href="<?php esc_url( bbp_forum_permalink( $widget_query->post->ID ) ); ?>" title="<?php esc_attr( bbp_forum_title( $widget_query->post->ID ) ); ?>">
							<?php bbp_forum_title( $widget_query->post->ID ); ?>
						</a>
						<span class="topic-count">
							<?php esc_html( bbp_forum_topic_count( $widget_query->post->ID ) ); ?>
						</span>
					</li>
				<?php } ?>
			</ul>

			<?php
			echo wp_kses_post( $after_widget );

			// Reset the $post global.
			wp_reset_postdata();

		}
	}

	/**
	 * Update the forum widget options
	 *
	 * @param array $new_instance The new instance options.
	 * @param array $old_instance The old instance options.
	 *
	 * @since bbPress (r2653)
	 */
	public function update( array $new_instance, array $old_instance ): array {
		$instance                 = $old_instance;
		$instance['title']        = wp_strip_all_tags( $new_instance['title'] );
		$instance['parent_forum'] = $new_instance['parent_forum'];

		// Force to any.
		if ( ! empty( $instance['parent_forum'] ) && ! is_numeric( $instance['parent_forum'] ) ) {
			$instance['parent_forum'] = 'any';
		}

		return $instance;
	}

	/**
	 * Output the forum widget options form
	 *
	 * @param mixed $instance Instance.
	 *
	 * @since bbPress (r2653)
	 * @uses  BBP_Forums_Widget::get_field_id() To output the field id
	 * @uses  BBP_Forums_Widget::get_field_name() To output the field name
	 */
	public function form( $instance ) {
		$title        = ! empty( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$parent_forum = ! empty( $instance['parent_forum'] ) ? esc_attr( $instance['parent_forum'] ) : '0';

		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'supportte' ); ?>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>"/>
			</label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'parent_forum' ) ); ?>"><?php esc_html_e( 'Parent Forum ID:', 'supportte' ); ?>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'parent_forum' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'parent_forum' ) ); ?>" type="text" value="<?php echo esc_attr( $parent_forum ); ?>"/>
			</label>

			<br/>

			<small><?php esc_html_e( '"0" to show only root - "any" to show all', 'supportte' ); ?></small>
		</p>
		<?php

	}
}
