<?php
/**
 * Plugin Name: SM Sticky Clicky Star
 * Plugin URI: http://sethmatics.com/extend/plugins/sm-sticky-clicky-star/
 * Description: Turn sticky (featured) posts on and off with 1 easy click! Control permissions with "User Role Editor".
 * Author: sethcarstens
 * Version: 1.1.1
 * Author URI: http://sethamtics.com/
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: sm_sticky_clicky_star
 *
 * @package sm-sticky-clicky-star
 * @category plugin
 * @author Seth Carstens <seth.carstens@gmail.com>
 */

//avoid direct calls to this file, because now WP core and framework has been used
if ( ! function_exists( 'add_filter' ) ) {
	header('Status: 403 Forbidden'); header('HTTP/1.1 403 Forbidden'); exit();
}

if(!class_exists('sm_sticky_clicky_star'))
{
	class sm_sticky_clicky_star
	{
		public $installed_dir;
		public $installed_url;

		/**
		 * Construct the plugin object
		 */
		public function __construct()
		{
			$this->installed_dir = dirname(__FILE__);
			$this->installed_url = plugins_url('/', __FILE__);
			//only load the sticky clicky star plugin functions if the user has the capability to "sticky" posts.
			add_action('init', array($this,'init'));
		} // END public function __construct

		/**
		 * Initialize the plugin -either for admin (back end) or public (front end)
		 */
		function init() {

			if(current_user_can('edit_others_posts') || current_user_can('edit_post_sticky') ) {
				require_once($this->installed_dir.'/sm-sticky-clicky-star-admin.php');
				new sm_sticky_clicky_star_admin($this);
			}
		}

		/**
		 * Activate the plugin
		 */
		public static function activate()
		{
			// get the "admin" role object and add capability
			$role = get_role( 'administrator' );
			$role->add_cap( 'edit_post_sticky' );
		} // END public static function activate

		/**
		 * Deactivate the plugin
		 */
		public static function deactivate()
		{
			global $wp_roles;
			foreach (array_keys($wp_roles->roles) as $role) {
				$wp_roles->remove_cap($role, 'edit_post_sticky');
			}
		} // END public static function deactivate

	} // END class sm_sticky_clicky_star
} // END if(!class_exists('sm_sticky_clicky_star'))

if(class_exists('sm_sticky_clicky_star'))
{
	// Installation and uninstallation hooks
	register_activation_hook(__FILE__, array('sm_sticky_clicky_star', 'activate'));
	register_deactivation_hook(__FILE__, array('sm_sticky_clicky_star', 'deactivate'));

	// instantiate the plugin class
	$sm_sticky_clicky_star = new sm_sticky_clicky_star();
}

