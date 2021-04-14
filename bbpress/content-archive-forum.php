<?php
/**
 * Archive Forum Content Part
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

	do_action( 'bbp_template_before_forums_index' );

	if ( bbp_has_forums() ) {
		bbp_get_template_part( 'loop', 'forums' );
	} else {
		bbp_get_template_part( 'feedback', 'no-forums' );
	}

	do_action( 'bbp_template_after_forums_index' );

	?>
</div>
