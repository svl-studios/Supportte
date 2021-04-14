<?php
/**
 * Archive Topic Content Part
 *
 * @package bbPress
 * @subpackage Theme
 */

defined( 'ABSPATH' ) || exit;

$args = array();

if ( ! is_user_logged_in() ) {
	$args = array(
		'show_stickies' => true,
		'meta_key'      => '_bbp_super_sticky_topics', // phpcs:ignore
	);
}

?>
<div id="bbpress-forums">
	<?php

	bbp_breadcrumb();

	if ( bbp_is_topic_tag() ) {
		bbp_topic_tag_description();
	}

	do_action( 'bbp_template_before_topics_index' );

	if ( bbp_has_topics( $args ) ) {
		bbp_get_template_part( 'loop', 'topics' );
		bbp_get_template_part( 'pagination', 'topics' );
	} else {
		bbp_get_template_part( 'feedback', 'no-topics' );
	}

	do_action( 'bbp_template_after_topics_index' );

	?>
</div>
