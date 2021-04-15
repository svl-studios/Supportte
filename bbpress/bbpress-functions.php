<?php
/**
 * Functions of the Default template-pack
 *
 * @package bbPress
 * @subpackage BBP_Theme_Compat
 * @since 2.1.0 bbPress (r3732)
 */

defined( 'ABSPATH' ) || exit;

/**
 * Theme Setup
 */
if ( ! class_exists( 'BBP_Default' ) ) {

	/**
	 * Loads bbPress Default Theme functionality
	 *
	 * This is not a real theme by WordPress standards, and is instead used as the
	 * fallback for any WordPress theme that does not have bbPress templates in it.
	 *
	 * To make your custom theme bbPress compatible and customize the templates, you
	 * can copy these files into your theme without needing to merge anything
	 * together; bbPress should safely handle the rest.
	 *
	 * See @link BBP_Theme_Compat() for more.
	 *
	 * @since 2.1.0 bbPress (r3732)
	 *
	 * @package bbPress
	 * @subpackage BBP_Theme_Compat
	 */
	/**
	 * Class BBP_Default
	 */
	class BBP_Default extends BBP_Theme_Compat {


		/**
		 * BBP_Default constructor.
		 *
		 * @param array $properties Properties.
		 */
		public function __construct( $properties = array() ) {
			parent::__construct(
				bbp_parse_args(
					$properties,
					array(
						'id'      => 'default',
						'name'    => 'bbPress Default',
						'version' => bbp_get_version(),
						'dir'     => trailingslashit( bbpress()->themes_dir . 'default' ),
						'url'     => trailingslashit( bbpress()->themes_url . 'default' ),
					),
					'default_theme'
				)
			);

			$this->setup_actions();
		}

		/**
		 * Setup the theme hooks
		 *
		 * @since 2.1.0 bbPress (r3732)
		 *
		 * @access private
		 */
		private function setup_actions() {
			add_action( 'bbp_enqueue_scripts', array( $this, 'enqueue_styles' ) ); // Enqueue theme CSS.
			add_action( 'bbp_enqueue_scripts', array( $this, 'enqueue_scripts' ) ); // Enqueue theme JS.
			add_filter( 'bbp_enqueue_scripts', array( $this, 'localize_topic_script' ) ); // Enqueue theme script localization.
			add_action( 'bbp_ajax_favorite', array( $this, 'ajax_favorite' ) ); // Handles the topic ajax favorite/unfavorite.
			add_action( 'bbp_ajax_subscription', array( $this, 'ajax_subscription' ) ); // Handles the topic ajax subscribe/unsubscribe.

			/**
			 * Template Wrappers.
			 */
			add_action( 'bbp_before_main_content', array( $this, 'before_main_content' ) ); // Top wrapper HTML.
			add_action( 'bbp_after_main_content', array( $this, 'after_main_content' ) ); // Bottom wrapper HTML.

			/**
			 * Override.
			 */
			do_action_ref_array( 'bbp_theme_compat_actions', array( &$this ) );
		}

		/**
		 * Inserts HTML at the top of the main content area to be compatible with
		 * the Twenty Twelve theme.
		 *
		 * @since 2.1.0 bbPress (r3732)
		 */
		public function before_main_content() {
			?>
			<div id="bbp-container">
			<div id="bbp-content" role="main">
			<?php
		}

		/**
		 * Inserts HTML at the bottom of the main content area to be compatible with
		 * the Twenty Twelve theme.
		 *
		 * @since 2.1.0 bbPress (r3732)
		 */
		public function after_main_content() {
			?>
			</div><!-- #bbp-content -->
			</div><!-- #bbp-container -->
			<?php
		}

		/**
		 * Load the theme CSS
		 *
		 * @since 2.1.0 bbPress (r3732)
		 */
		public function enqueue_styles() {

			// Setup the default styling.
			$defaults = array(
				'bbp-default' => array(
					'file'         => 'css/bbpress.css',
					'dependencies' => array(),
				),
			);

			// Optionally support an RTL variant.
			if ( is_rtl() ) {
				$defaults['bbp-default-rtl'] = array(
					'file'         => 'css/bbpress-rtl.css',
					'dependencies' => array(),
				);
			}

			// Get and filter the bbp-default style.
			$styles = apply_filters( 'bbp_default_styles', $defaults );

			// Enqueue the styles.
			foreach ( $styles as $handle => $attributes ) {
				bbp_enqueue_style( $handle, $attributes['file'], $attributes['dependencies'], $this->version );
			}
		}

		/**
		 * Enqueue the required JavaScript files
		 *
		 * @since 2.1.0 bbPress (r3732)
		 */
		public function enqueue_scripts() {

			// Setup scripts array.
			$scripts = array();

			// Editor scripts.
			// @see https://bbpress.trac.wordpress.org/ticket/2930.
			if ( bbp_use_wp_editor() && is_bbpress() ) {
				$scripts['bbpress-editor'] = array(
					'file'         => 'js/editor.js',
					'dependencies' => array( 'jquery' ),
				);
			}

			// Forum-specific scripts.
			if ( bbp_is_single_forum() ) {
				$scripts['bbpress-engagements'] = array(
					'file'         => 'js/engagements.js',
					'dependencies' => array( 'jquery' ),
				);
			}

			// Topic-specific scripts.
			if ( bbp_is_single_topic() || bbp_is_topic_edit() ) {

				// Engagements.
				$scripts['bbpress-engagements'] = array(
					'file'         => 'js/engagements.js',
					'dependencies' => array( 'jquery' ),
				);

				// Hierarchical replies.
				if ( bbp_thread_replies() ) {
					$scripts['bbpress-reply'] = array(
						'file'         => 'js/reply.js',
						'dependencies' => array( 'jquery' ),
					);
				}
			}

			// User Profile edit.
			if ( bbp_is_single_user_edit() ) {
				wp_enqueue_script( 'user-profile' );
			}

			// Filter the scripts.
			$scripts = apply_filters( 'bbp_default_scripts', $scripts );

			// Enqueue the scripts.
			foreach ( $scripts as $handle => $attributes ) {
				bbp_enqueue_script( $handle, $attributes['file'], $attributes['dependencies'], $this->version, true );
			}
		}

		/**
		 * Load localizations for topic script
		 *
		 * These localizations require information that may not be loaded even by init.
		 *
		 * @since 2.1.0 bbPress (r3732)
		 */
		public function localize_topic_script() {

			// Single forum or topic.
			if ( bbp_is_single_forum() || bbp_is_single_topic() ) {
				wp_localize_script(
					'bbpress-engagements',
					'bbpEngagementJS',
					array(
						'object_id'          => get_the_ID(),
						'bbp_ajaxurl'        => bbp_get_ajax_url(),
						'generic_ajax_error' => esc_html__( 'Something went wrong. Refresh your browser and try again.', 'bbpress' ),
					)
				);
			}
		}

		/**
		 * AJAX handler to add or remove a topic from a user's favorites
		 *
		 * @since 2.1.0 bbPress (r3732)
		 */
		public function ajax_favorite() {

			// Bail if favorites are not active.
			if ( ! bbp_is_favorites_active() ) {
				bbp_ajax_response( false, esc_html__( 'Favorites are no longer active.', 'bbpress' ), 300 );
			}

			// Bail if user is not logged in.
			if ( ! is_user_logged_in() ) {
				bbp_ajax_response( false, esc_html__( 'Please login to favorite.', 'bbpress' ), 301 );
			}

			// Get user and topic data.
			$user_id = bbp_get_current_user_id();
			$id      = ! empty( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
			$type    = ! empty( $_POST['type'] ) ? sanitize_key( $_POST['type'] ) : 'post';

			// Bail if user cannot add favorites for this user.
			if ( ! current_user_can( 'edit_user', $user_id ) ) {
				bbp_ajax_response( false, esc_html__( 'You do not have permission to do this.', 'bbpress' ), 302 );
			}

			// Get the object.
			if ( 'post' === $type ) {
				$object = get_post( $id );
			}

			// Bail if topic cannot be found.
			if ( empty( $object ) ) {
				bbp_ajax_response( false, esc_html__( 'Favorite failed.', 'bbpress' ), 303 );
			}

			// Bail if user did not take this action.
			if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['nonce'] ) ), 'toggle-favorite_' . $object->ID ) ) {
				bbp_ajax_response( false, esc_html__( 'Are you sure you meant to do that?', 'bbpress' ), 304 );
			}

			// Take action.
			$status = bbp_is_user_favorite( $user_id, $object->ID )
					? bbp_remove_user_favorite( $user_id, $object->ID )
					: bbp_add_user_favorite( $user_id, $object->ID );

			// Bail if action failed.
			if ( empty( $status ) ) {
				bbp_ajax_response( false, esc_html__( 'The request was unsuccessful. Please try again.', 'bbpress' ), 305 );
			}

			// Put subscription attributes in convenient array.
			$attrs = array(
				'object_id'   => $object->ID,
				'object_type' => $type,
				'user_id'     => $user_id,
			);

			// Action succeeded.
			bbp_ajax_response( true, bbp_get_user_favorites_link( $attrs, $user_id, false ), 200 );
		}

		/**
		 * AJAX handler to Subscribe/Unsubscribe a user from a topic
		 *
		 * @since 2.1.0 bbPress (r3732)
		 */
		public function ajax_subscription() {

			// Bail if subscriptions are not active.
			if ( ! bbp_is_subscriptions_active() ) {
				bbp_ajax_response( false, esc_html__( 'Subscriptions are no longer active.', 'bbpress' ), 300 );
			}

			// Bail if user is not logged in.
			if ( ! is_user_logged_in() ) {
				bbp_ajax_response( false, esc_html__( 'Please login to subscribe.', 'bbpress' ), 301 );
			}

			// Get user and topic data.
			$user_id = bbp_get_current_user_id();
			$id      = ! empty( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
			$type    = ! empty( $_POST['type'] ) ? sanitize_key( $_POST['type'] ) : 'post';

			// Bail if user cannot add favorites for this user.
			if ( ! current_user_can( 'edit_user', $user_id ) ) {
				bbp_ajax_response( false, esc_html__( 'You do not have permission to do this.', 'bbpress' ), 302 );
			}

			// Get the object.
			if ( 'post' === $type ) {
				$object = get_post( $id );
			}

			// Bail if topic cannot be found.
			if ( empty( $object ) ) {
				bbp_ajax_response( false, esc_html__( 'Subscription failed.', 'bbpress' ), 303 );
			}

			// Bail if user did not take this action.
			if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['nonce'] ) ), 'toggle-subscription_' . $object->ID ) ) {
				bbp_ajax_response( false, esc_html__( 'Are you sure you meant to do that?', 'bbpress' ), 304 );
			}

			// Take action.
			$status = bbp_is_user_subscribed( $user_id, $object->ID )
					? bbp_remove_user_subscription( $user_id, $object->ID )
					: bbp_add_user_subscription( $user_id, $object->ID );

			// Bail if action failed.
			if ( empty( $status ) ) {
				bbp_ajax_response( false, esc_html__( 'The request was unsuccessful. Please try again.', 'bbpress' ), 305 );
			}

			// Put subscription attributes in convenient array.
			$attrs = array(
				'object_id'   => $object->ID,
				'object_type' => $type,
				'user_id'     => $user_id,
			);

			// Add separator to topic if favorites is active.
			if ( ( 'post' === $type ) && ( bbp_get_topic_post_type() === get_post_type( $object ) ) && bbp_is_favorites_active() ) {
				$attrs['before'] = '&nbsp;|&nbsp;';
			}

			// Action succeeded.
			bbp_ajax_response( true, bbp_get_user_subscribe_link( $attrs, $user_id, false ), 200 );
		}
	}

	new BBP_Default();
}

/**
 * Custom functions to override BBPress default functionalities
 */

/**
 * Modify bbp_get_time_since() output
 */
add_filter( 'bbp_get_time_since', 'aq_get_time_since', 10, 3 );

/**
 * Aqua Get time since.
 *
 * @param string $output     Output.
 * @param string $older_date Older date.
 * @param string $newer_date Newer date.
 *
 * @return mixed|string|void
 */
function aq_get_time_since( string $output, string $older_date, string $newer_date ) {

	// Setup the strings.
	$unknown_text   = apply_filters( 'bbp_core_time_since_unknown_text', esc_html__( 'sometime', 'bbpress' ) );
	$right_now_text = apply_filters( 'bbp_core_time_since_right_now_text', esc_html__( 'right now', 'bbpress' ) );
	$ago_text       = apply_filters( 'bbp_core_time_since_ago_text', esc_html__( '%s ago', 'bbpress' ) );

	// array of time period chunks.
	$chunks = array(
		array( 60 * 60 * 24 * 365, esc_html__( 'year', 'bbpress' ), esc_html__( 'years', 'bbpress' ) ),
		array( 60 * 60 * 24 * 30, esc_html__( 'month', 'bbpress' ), esc_html__( 'months', 'bbpress' ) ),
		array( 60 * 60 * 24 * 7, esc_html__( 'week', 'bbpress' ), esc_html__( 'weeks', 'bbpress' ) ),
		array( 60 * 60 * 24, esc_html__( 'day', 'bbpress' ), esc_html__( 'days', 'bbpress' ) ),
		array( 60 * 60, esc_html__( 'hour', 'bbpress' ), esc_html__( 'hours', 'bbpress' ) ),
		array( 60, esc_html__( 'minute', 'bbpress' ), esc_html__( 'minutes', 'bbpress' ) ),
		array( 1, esc_html__( 'second', 'bbpress' ), esc_html__( 'seconds', 'bbpress' ) ),
	);

	if ( ! empty( $older_date ) && ! is_numeric( $older_date ) ) {
		$time_chunks = explode( ':', str_replace( ' ', ':', $older_date ) );
		$date_chunks = explode( '-', str_replace( ' ', '-', $older_date ) );
		$older_date  = gmmktime( (int) $time_chunks[1], (int) $time_chunks[2], (int) $time_chunks[3], (int) $date_chunks[1], (int) $date_chunks[2], (int) $date_chunks[0] );
	}

	// $newer_date will equal false if we want to know the time elapsed
	// between a date and the current time. $newer_date will have a value if
	// we want to work out time elapsed between two known dates.
	$newer_date = ( ! $newer_date ) ? strtotime( current_time( 'mysql' ) ) : $newer_date;

	// Difference in seconds.
	$since = $newer_date - $older_date;

	// Something went wrong with date calculation and we ended up with a negative date.
	if ( 0 > $since ) {
		$output = $unknown_text;

		/**
		 * We only want to output two chunks of time here, eg:
		 * x years, xx months
		 * x days, xx hours
		 * so there's only two bits of calculation below
		 */
	} else {

		// Step one: the first chunk.
		for ( $i = 0, $j = count( $chunks ); $i < $j; ++ $i ) {
			$seconds = $chunks[ $i ][0];

			// Finding the biggest chunk (if the chunk fits, break).
			$count = floor( $since / $seconds );
			if ( 0 !== (int) $count ) {
				break;
			}
		}

		// If $i iterates all the way to $j, then the event happened 0 seconds ago.
		if ( ! isset( $chunks[ $i ] ) ) {
			$output = $right_now_text;

		} else {

			// Set output var.
			$output = ( 1 === (int) $count ) ? '1 ' . $chunks[ $i ][1] : $count . ' ' . $chunks[ $i ][2];

			// No output, so happened right now.
			if ( ! (int) trim( $output ) ) {
				$output = $right_now_text;
			}
		}
	}

	return $output;
}

/**
 * Modify status display in single topic
 */
remove_action( 'bbp_template_before_single_topic', 'bbps_add_support_forum_features' );
add_action( 'bbp_template_before_single_topic', 'aq_add_support_forum_features' );

/**
 * Support forum features.
 */
function aq_add_support_forum_features() {

	// Only display all this stuff if the support forum option has been selected.
	if ( bbps_is_support_forum( bbp_get_forum_id() ) ) {
		$can_edit = (bool) bbps_get_update_capabilities();
		$topic_id = bbp_get_topic_id();
		$status   = bbps_get_topic_status( $topic_id );
		$forum_id = bbp_get_forum_id();
		$user_id  = get_current_user_id();

		// Get out the option to tell us who is allowed to view and update the drop down list.
		if ( true === $can_edit ) {

			?>
			<div id="bbps_support_forum_options">
				<?php bbps_generate_status_options( $topic_id, $status ); ?>
			</div>
			<?php
		}

		// Has the user enabled the move topic feature?.
		if ( ( true === (bool) get_option( '_bbps_enable_topic_move' ) ) && ( current_user_can( 'administrator' ) || current_user_can( 'bbp_moderator' ) ) ) {

			?>
			<div id="bbps_support_forum_move">
				<form id="bbps-topic-move" name="bbps_support_topic_move" action="" method="post">
					<label for="bbp_forum_id">Move topic to: </label><?php bbp_dropdown(); ?>
					<input type="submit" value="Move" name="bbps_topic_move_submit"/>
					<input type="hidden" value="bbps_move_topic" name="bbps_action"/>
					<input type="hidden" value="<?php echo esc_attr( $topic_id ); ?>" name="bbps_topic_id"/>
					<input type="hidden" value="<?php echo esc_attr( $forum_id ); ?>" name="bbp_old_forum_id"/>
				</form>
			</div>
			<?php
		}
	}
}

/**
 * Adds Status to topic title
 */
remove_action( 'bbp_theme_before_topic_title', 'bbps_modify_title' );
add_action( 'bbp_theme_before_topic_title', 'aq_modify_before_title', 10, 2 );

/**
 * Modify before title.
 *
 * @param string $title    Title.
 * @param mixed  $topic_id Topics ID.
 */
function aq_modify_before_title( string $title, $topic_id = 0 ) {
	$topic_id = bbp_get_topic_id( $topic_id );

	$replies   = bbp_get_topic_reply_count( $topic_id );
	$statuses  = array( 1, 2, 3 );
	$status_id = get_post_meta( $topic_id, '_bbps_topic_status', true );

	// Let's not override default closed/sticky status.
	if ( bbp_is_topic_sticky() ) {
		echo '<span class="topic-sticky"> [Sticky] </span>';

		// Let's not override the default statuses.
	} elseif ( ! in_array( $status_id, $statuses, true ) ) {
		if ( $replies >= 1 ) {
			echo '<span class="in-progress"> [In Progress] </span>';
		} else {
			echo '<span class="not-resolved"> [Not Resolved] </span>';
		}

		// Default Statuses.
	} else {
		if ( 1 === $status_id ) { // Not Resolved.
			echo '<span class="not-resolved"> [Not Resolved] </span>';
		}

		if ( 2 === $status_id ) { // Not Resolved.
			echo '<span class="resolved"> [Resolved] </span>';
		}

		if ( 3 === $status_id ) { // Not Support Question (mark as resolved).
			add_post_meta( $topic_id, '_bbps_topic_status', 2 );

			echo '<span class="resolved"> [Resolved] </span>';
		}
	}
}

/**
 * Display Topic Status.
 *
 * @param int $topic_id Topic ID.
 */
function aq_display_topic_status( $topic_id = 0 ) {
	$topic_id = $topic_id ? $topic_id : bbp_get_topic_id();

	$statuses  = array( 1, 2, 3 );
	$status_id = (int) get_post_meta( $topic_id, '_bbps_topic_status', true );

	echo '<div class="aq-topic-status">';

	if ( bbp_is_topic_sticky() ) {
		echo '<span class="sticky">Sticky</span>';
	} elseif ( in_array( $status_id, $statuses, true ) ) {
		if ( 1 === $status_id ) {
			echo '<span class="not-resolved">Not Resolved</span>';
		}

		if ( 2 === $status_id ) {
			echo '<span class="resolved">Resolved</span>';
		}

		if ( 3 === $status_id ) {
			echo '<span class="in-progress">In Progress</span>';
		}
	} elseif ( bbp_is_topic_closed() ) {
		echo '<span class="sticky">Sticky</span>';
	} else {
		echo '<span class="in-progress">In Progress</span>';
	}

	echo '</div>';
}

/** Disable admin bar completely for non-admin */
if ( ! function_exists( 'disable_admin_bar' ) ) {

	/**
	 * Disable admin bar.
	 */
	function disable_admin_bar() {
		remove_action( 'admin_footer', 'wp_admin_bar_render', 1000 ); // for the admin page.
		remove_action( 'wp_footer', 'wp_admin_bar_render', 1000 ); // for the front end.

		/**
		 * Remove backend style.
		 */
		function remove_admin_bar_style_backend() {
			echo '<style>body.admin-bar #wpcontent, body.admin-bar #adminmenu { padding-top: 0px !important; }</style>';
		}

		add_filter( 'admin_head', 'remove_admin_bar_style_backend' );

		/**
		 * CSS override for the frontend.
		 */
		function remove_admin_bar_style_frontend() {
			echo '<style media="screen">
      html { margin-top: 0px !important; }
      * html body { margin-top: 0px !important; }
      </style>';
		}

		add_filter( 'wp_head', 'remove_admin_bar_style_frontend', 99 );
	}
}

if ( ! current_user_can( 'manage_options' ) ) {
	add_action( 'init', 'disable_admin_bar' ); // New version.
}

/**
 * Remove topic & reply revision log
 */
remove_filter( 'bbp_get_reply_content', 'bbp_reply_content_append_revisions', 1, 2 );
remove_filter( 'bbp_get_topic_content', 'bbp_topic_content_append_revisions', 1, 2 );

/**
 * Custom function from bbp_get_author_link(), returns only author name.
 *
 * @param mixed $post_id Post ID.
 *
 * @return string
 */
function aq_get_author( $post_id = 0 ): string {

	// Confirmed topic.
	if ( bbp_is_topic( $post_id ) ) {
		return bbp_get_topic_author( $post_id );

		// Confirmed reply.
	} elseif ( bbp_is_reply( $post_id ) ) {
		return bbp_get_reply_author( $post_id );

		// Get the post author and proceed.
	} else {
		$user_id = get_post_field( 'post_author', $post_id );
	}

	// Neither a reply nor a topic, so could be a revision.
	if ( ! empty( $post_id ) ) {

		// Assemble some link bits.
		$anonymous = bbp_is_reply_anonymous( $post_id );

		// Add links if not anonymous.
		if ( empty( $anonymous ) && bbp_user_has_profile( $user_id ) ) {

			$author_link = get_the_author_meta( 'display_name', $user_id );

			// No links if anonymous.
		} else {
			$author_link = join( '&nbsp;', $author_links );
		}

		// No post so link is empty.
	} else {
		$author_link = '';
	}

	return $author_link;
}

/**
 * Adds search query to topic pagination
 */
add_filter( 'bbp_topic_pagination', 'aq_topic_pagination_query' );

/**
 * Topic pagination query.
 *
 * @param array $bbp_topic_pagination Pagination.
 *
 * @return array
 */
function aq_topic_pagination_query( $bbp_topic_pagination = array() ): array {
	$http_get = ( 'GET' === sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ?? '' ) ) );

	if ( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_wpnonce'] ) ), 'supportte_search' ) ) {
		$search = $http_get ? ( sanitize_text_field( wp_unslash( $_GET['s'] ?? '' ) ) ) : '';

		if ( $search ) {
			$bbp_topic_pagination['add_args'] = array( 'q' => $search );
		}
	} else {
		if ( isset( $_GET['_wpnonce'] ) ) {
			wp_nonce_ays( 'expired' );
		}
	}

	return $bbp_topic_pagination;
}

/**
 * Change "search-posts" to other base (optional).
 */
add_action( 'init', 'wpse21549_init' );

/**
 * Global rewrite.
 */
function wpse21549_init() {
	$GLOBALS['wp_rewrite']->search_base = 'search-posts';
}

/**
 * Change user roles naming.
 *
 * @param string $role    Role.
 * @param mixed  $user_id User ID.
 *
 * @return string
 */
function aq_custom_bbp_roles( string $role, $user_id ): string {
	if ( 'Key Master' === $role ) {
		return 'Admin';
	}

	if ( 'Participant' === $role ) {
		return 'Member';
	}

	return $role;
}

add_filter( 'bbp_get_user_display_role', 'aq_custom_bbp_roles', 10, 2 );
