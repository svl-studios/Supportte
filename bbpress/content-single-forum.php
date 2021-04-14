<?php

/**
 * Single Forum Content Part
 *
 * @package bbPress
 * @subpackage Theme
 */

defined( 'ABSPATH' ) || exit;

?>
<div id="bbpress-forums" class="bbpress-wrapper">
	<?php

	bbp_breadcrumb();
	bbp_forum_subscription_link();

	do_action( 'bbp_template_before_single_forum' );

	if ( post_password_required() ) {
		bbp_get_template_part( 'form', 'protected' );
	} else {
		if ( is_user_logged_in() ) {
			bbp_single_forum_description();
			$args = array();
		} else {
			$args = array(
				'show_stickies' => true,
				'meta_key'      => '_bbp_super_sticky_topics',
			);
		}

		if ( bbp_has_forums() ) {
			bbp_get_template_part( 'loop', 'forums' );
		}

		if ( ! bbp_is_forum_category() && bbp_has_topics( $args ) ) {
			bbp_get_template_part( 'loop', 'topics' );
			bbp_get_template_part( 'pagination', 'topics' );
			bbp_get_template_part( 'form', 'topic' );

		} elseif ( ! bbp_is_forum_category() ) {
			bbp_get_template_part( 'feedback', 'no-topics' );
			bbp_get_template_part( 'form', 'topic' );
		}
	}

	do_action( 'bbp_template_after_single_forum' );

	?>
</div>
