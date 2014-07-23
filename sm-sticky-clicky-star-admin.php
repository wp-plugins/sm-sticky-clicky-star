<?php
//avoid direct calls to this file, because now WP core and framework has been used
if ( ! function_exists( 'add_filter' ) ) {
	header('Status: 403 Forbidden'); header('HTTP/1.1 403 Forbidden'); exit();
}

if(!class_exists('sm_sticky_clicky_star_admin'))
{
	class sm_sticky_clicky_star_admin
	{
		public $installed_dir;
		public $installed_url;

		public function __construct($plugin)
		{
			$this->installed_dir = $plugin->installed_dir;
			$this->installed_url = $plugin->installed_url;

			add_action('post_submitbox_misc_actions', array($this, 'sticky_meta'));
			add_filter('manage_posts_columns', array($this,'add_sticky_column'));
			//adds sticky star to appthemes themes
			if(defined('APP_POST_TYPE')){
				add_filter('manage_edit-'.APP_POST_TYPE.'_columns',array($this, 'add_sticky_column'));
			}
			add_action('manage_posts_custom_column',  array($this,'sticky_column_content'));
			add_action('wp_ajax_sm_sticky', array($this,'sticky_callback'));

			//load style and js on pages that need it, note that admin_enqueue_styles does not work as of WP 4.0.0
			add_action('admin_enqueue_scripts', array($this,'click_to_stick_styles'));
			add_action('admin_enqueue_scripts', array($this,'click_to_stick_scripts'));
		}

		function add_sticky_column ($columns) {
			$columns['sticky'] = 'Sticky';
			return $columns;
		}

		//add admin stylesheet
		function click_to_stick_styles($hook) {
			if( $hook == 'edit.php' || $hook == 'post.php') {
				wp_enqueue_style('sm_click_to_stick_styles', $this->installed_url.'css/sm-click-to-stick.css', array(), '1.0.0', 'all');
			}
		}

		// add admin javascript
		function click_to_stick_scripts($hook) {
			if( $hook == 'edit.php' || $hook == 'post.php') {
				wp_enqueue_script( 'sm_click_to_stick_scripts', $this->installed_url.'js/sm-click-to-stick.js', array('jquery') );
			}
		}

		function sticky_meta() {
			global $post;
			if($post->post_type !='page')
				echo '<div id="smSticky" class="misc-pub-section ">Make Sticky: '.$this->get_sticky_link($post->ID).'</div>';
		}

		function sticky_column_content($name) {
			global $post;
			if($name=='sticky')
				echo $this->get_sticky_link($post->ID);
		}

		function get_sticky_link($thePostID = '') {
			global $post;
			if($thePostID == '')
				$thePostID = $post->ID;
			$stickyClass = '';
			$stickyTitle = 'Make Sticky';
			if(is_sticky($thePostID)) {
				$stickyClass = 'isSticky';
				$stickyTitle = 'Remove Sticky';
			}
			$stickyLink = '<a href="id='.$thePostID.'&code='.wp_create_nonce('sm-sticky-nonce').'" id="smClickToStick'.$thePostID.'" class="smClickToStick '.$stickyClass.'" title="'.$stickyTitle.'"></a>';
			return $stickyLink;
		}

		function sticky_callback() {
			if ( !wp_verify_nonce( $_POST['code'], 'sm-sticky-nonce' ) ) {
				// failed nonce validation
				echo 'failed nonce: '.$_POST['anthem_nonce'];die();
			}

			$stickyPosts = get_option('sticky_posts');

			if(!is_array($stickyPosts))
				$stickyPosts = array();

			if (in_array($_POST['id'], $stickyPosts)) {
				$removeKey = array_search($_POST['id'], $stickyPosts);
				unset($stickyPosts[$removeKey]);
				$stickyResult = 'removed';
			}
			else {
				array_unshift($stickyPosts, $_POST['id']);
				//$stickyPost[] = $_POST['id'];
				$stickyResult = 'added';
			}

			if(update_option('sticky_posts', $stickyPosts))
				echo $stickyResult;
			else
				echo 'An error occured';

			die(); // this is required to return a proper result
		}
	}
}







