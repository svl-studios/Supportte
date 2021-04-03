<?php
/**
 * Topbar.
 *
 * @package     Supportte
 * @author      SVL Studios
 * @copyright   Copyright (c) 2021, SVL Studios
 * @link        https://www.svlstudios.com
 * @access      https://github.com/svl-studios/supportte
 * @since       Supportte 1.0.0
 */

defined( 'ABSPATH' ) || exit;

?>
<div id="topbar">
	<div class="inner">
		<div class="left">
			Welcome to SVL Studios Support Forum!
		</div>
		<div class="right">
			<?php
			$redirect = home_url();

			if ( is_user_logged_in() ) {
				echo '<a href="' . esc_url( bbp_get_user_profile_edit_url( bbp_get_user_id( '', false, true ) ) ) . '">' . esc_html__( 'Edit Profile', 'supportte' ) . '</a> ' . esc_html__( 'or', 'supportte' ) . ' ';
				echo '<a href="' . esc_url( wp_logout_url( $redirect ) ) . '">' . esc_html__( 'Logout', 'supportte' ) . '</a>';
			} else {
				echo '<a href="' . esc_url( wp_login_url( $redirect ), $force_reauth = false ) . '">' . esc_html__( 'Login', 'supportte' ) . '</a> ' . esc_html__( 'or', 'supportte' ) . ' ';
				echo '<a href="' . esc_url( wp_login_url() ) . '?action=register">Register</a>';
			}
			?>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
