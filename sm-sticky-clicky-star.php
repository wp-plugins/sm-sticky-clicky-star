<?php
/*
Plugin Name: SM Sticky Clicky Star
Plugin URI: http://sethmatics.com/extend/plugins/sm-sticky-clicky-star/
Description: Turn sticky (featured) posts on and off with 1 easy click! Control permissions with "User Role Editor".
Author: sethmatics, bigj9901
Version: 1.0.7
Author URI: http://sethamtics.com/
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: sm_sticky_clicky_star
*/

//avoid direct calls to this file, because now WP core and framework has been used
if ( ! function_exists( 'add_filter' ) ) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}

define('sm_sticky_clicky_star_DIR', WP_PLUGIN_DIR.'/sm-sticky-clicky-star/');

// get the "author" role object
$role = get_role( 'administrator' );
$role->add_cap( 'edit_post_sticky' );

//only load the sticky clicky star plugin functions if the user has the capability to "sticky" posts.
add_action('init', 'sm_sticky_clicky_star_init');
function sm_sticky_clicky_star_init() {
	if(current_user_can('edit_others_posts') || current_user_can('edit_post_sticky') ) {
		require_once(sm_sticky_clicky_star_DIR.'sm-sticky-clicky-star-functions.php');
	}
}