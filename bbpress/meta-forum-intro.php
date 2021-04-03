<?php
/**
 * Meta Forum Intro
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
<div id="forums-search">
	<form role="search" method="get" id="searchform" class="searchform" action="<?php echo esc_url( site_url( '/search' ) ); ?>">
		<input type="text" value="" name="q" class="search" placeholder="Search the forums..">
		<input type="submit" class="searchsubmit" value="Search">
	</form>
</div>
<br/>
<hr/>
