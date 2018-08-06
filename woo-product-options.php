<?php
/*
 * Plugin Name: Woo Product Options
 * Description: Easy and flexible way to display extra product options in product page.
 * Plugin URI:
 * Author URI: https://cybercraftit.com/
 * Author: CyberCraft
 * Text Domain: woopo
 * Domain Path: /languages
 * Version: 1.0
 * License: GPL2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WOOPO_VERSION', '1.0' );
define( 'WOOPO_ROOT', dirname(__FILE__) );
define( 'WOOPO_ASSET_PATH', plugins_url('assets',__FILE__) );
define( 'WOOPO_BASE_FILE', __FILE__ );
define( 'WOOPO_PRODUCTION', true );

Class Woopo_Init {
	/**
	 * @var Singleton The reference the *Singleton* instance of this class
	 */
	private static $instance;

	/**
	 * Returns the *Singleton* instance of this class.
	 *
	 * @return Singleton The *Singleton* instance.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function __construct() {
		register_activation_hook( __FILE__, array( $this, 'on_activate' ) );
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array( $this, 'action_links' ) );
		add_action( 'admin_head', array( $this, 'admin_head_includes' ) );
		$this->includes();
	}

	public function on_activate() {

	}

	public function action_links($links) {
		$links[] = '<a href="https://cybercraftit.com/contact/" target="_blank">'.__( 'Ask for Modification', 'woopo' ).'</a>';
		if( ! Woopo_Functions::is_pro() ) {
			$links[] = '<a href="https://cybercraftit.com/woo-product-options-pro/" style="color: #fa0000;" target="_blank">'.__( 'Upgrade to Pro', 'woopo' ).'</a>';
		}
		return $links;
	}

	public function includes() {
		include_once 'form-type-data.php';
		include_once 'ajax-actions.php';
		include_once 'form-builder-admin.php';
		include_once 'functions.php';

		include_once 'shortcodes/form.php';
		include_once 'shortcodes/shortcode.php';

		include_once 'submission-process.php';
		include_once 'woopo-applications.php';
		include_once 'admin-product-single.php';

		include_once 'public-single-product.php';

		require_once dirname(__FILE__).'/news.php';

		if( Woopo_Functions::is_pro() ) {
			include_once 'pro/loader.php';
		} else {
			include_once 'pro-data.php';
		}
	}

	public function admin_head_includes() {
		include_once 'data.php';
	}


	public function register_post_type() {
		$capability = Woopo_Functions::form_capability();

		$labels = array(
			'name'                  => _x('Form', 'post type general name', 'woopo'),
			'singular_name'         => _x('Form', 'post type singular name','woopo'),
			'menu_name'             => _x( 'Form', 'admin menu', 'woopo'),
			'name_admin_bar'        => _x( 'Form', 'add new on admin bar', 'woopo'),
			'add_new'               => _x('Add New Form', 'Form' , 'woopo' ),
			'add_new_item'          => __('Add New Form', 'woopo'),
			'edit_item'             => __('Edit Form', 'woopo'),
			'new_item'              => __('New Form' , 'woopo' ),
			'view_item'             => __('View Form', 'woopo' ),
			'all_items'             => __( 'All Form', 'woopo' ),
			'search_items'          => __('Search Form', 'woopo' ),
			'not_found'             =>  __('Nothing found', 'woopo' ),
			'not_found_in_trash'    => __('Nothing found in Trash', 'woopo' ),
			'parent_item_colon'     => '',

		);

		register_post_type( 'woopo_form', array(
			'label'           => __( 'Forms', 'woopo' ),
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => false,
			'capability_type' => 'post',
			'hierarchical'    => false,
			'query_var'       => false,
			'supports'        => array('title'),
			'capabilities' => array(
				'publish_posts'       => $capability,
				'edit_posts'          => $capability,
				'edit_others_posts'   => $capability,
				'delete_posts'        => $capability,
				'delete_others_posts' => $capability,
				'read_private_posts'  => $capability,
				'edit_post'           => $capability,
				'delete_post'         => $capability,
				'read_post'           => $capability,
			),
			'labels' => $labels,
		) );

		/**
		 * Entry post type
		 */
		$labels = array(
			'name'                  => _x('Entry', 'post type general name', 'woopo'),
			'singular_name'         => _x('Entry', 'post type singular name','woopo'),
			'menu_name'             => _x( 'Entry', 'admin menu', 'woopo'),
			'name_admin_bar'        => _x( 'Entry', 'add new on admin bar', 'woopo'),
			'add_new'               => _x('Add New Entry', 'Form' , 'woopo' ),
			'add_new_item'          => __('Add New Entry', 'woopo'),
			'edit_item'             => __('Edit Entry', 'woopo'),
			'new_item'              => __('New Entry' , 'woopo' ),
			'view_item'             => __('View Entry', 'woopo' ),
			'all_items'             => __( 'All Entry', 'woopo' ),
			'search_items'          => __('Search Entry', 'woopo' ),
			'not_found'             =>  __('Nothing found', 'woopo' ),
			'not_found_in_trash'    => __('Nothing found in Trash', 'woopo' ),
			'parent_item_colon'     => '',

		);

		register_post_type( 'woopo_entry', array(
			'label'           => __( 'Entries', 'woopo' ),
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => false,
			'capability_type' => 'post',
			'hierarchical'    => false,
			'query_var'       => false,
			'supports'        => array('title'),
			'capabilities' => array(
				'publish_posts'       => $capability,
				'edit_posts'          => $capability,
				'edit_others_posts'   => $capability,
				'delete_posts'        => $capability,
				'delete_others_posts' => $capability,
				'read_private_posts'  => $capability,
				'edit_post'           => $capability,
				'delete_post'         => $capability,
				'read_post'           => $capability,
			),
			'labels' => $labels,
		) );
	}
}

Woopo_Init::get_instance();

