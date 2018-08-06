<?php

class Woopo_Admin {
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
        add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );
        add_action( 'admin_footer', array( $this, 'admin_footer' ) );
        add_action( 'woopo_prepend_scripts_styles', array( $this, 'form_type_data' ) );
        add_action( 'woopo_prepend_scripts_styles', array( $this, 'prepend_scripts_styles' ) );
    }

    public function register_admin_menu() {
        global $submenu;

        $capability = Woopo_Functions::form_capability();
        $hook = add_submenu_page( 'edit.php?post_type=product', __( 'Woo Product Options - The Best and Fastest Form Builder Ever', 'woopo' ), __( 'Woo Product Options', 'woopo' ), $capability, 'woopo', array( $this, 'woopo_page') );

        if ( current_user_can( $capability ) ) {
            $submenu = apply_filters( 'woopo_admin_menu', $submenu, $hook, $capability );
            do_action( 'woopo_admin_menu', $submenu, $hook, $capability );
        }

        // only admins should see the settings page
        if ( current_user_can( 'manage_options' ) ) {
            $submenu['edit.php?post_type=product'][] = array( __( 'Product Options Settings', 'woopo' ), 'manage_options', 'edit.php?post_type=product&page=woopo#/settings' );
        }

	    $submenu['edit.php?post_type=product'][] = array( __( 'Help', 'woopo' ), 'manage_options', 'edit.php?post_type=product&page=woopo#/help' );

        if( !Woopo_Functions::is_pro() ) {
	        $submenu['edit.php?post_type=product'][] = array( __( 'Upgrage Woo Product Options', 'woopo' ), 'manage_options', 'edit.php?post_type=product&page=woopo#/get-pro' );
        }

	    $submenu['edit.php?post_type=product'][] = array( __( 'News ', 'woopo' ), 'manage_options', 'edit.php?post_type=product&page=woopo#/cc-news' );

        add_action( 'load-'. $hook, array( $this, 'load_scripts' ) );
    }


    public function woopo_page() {
        include_once WOOPO_ROOT.'/templates/main.php';
    }

    public function load_scripts() {
        wp_enqueue_script( 'jquery-ui-sortable' );
        wp_enqueue_style('woopo-framework-css', WOOPO_ASSET_PATH.'/css/framework.css' );
        wp_enqueue_style('woopo-style-css', WOOPO_ASSET_PATH.'/css/style.css' );
        wp_enqueue_style('woopo-element-css', WOOPO_ASSET_PATH.'/css/element.css' );

        if( !WOOPO_PRODUCTION ) {
	        wp_enqueue_script('woopo-vue', WOOPO_ASSET_PATH.'/js/vue.js', array(), false, true );
        } else {
	        wp_enqueue_script('woopo-vue', WOOPO_ASSET_PATH.'/js/vue.min.js', array(), false, true );
        }

        wp_enqueue_script('woopo-vue-router', WOOPO_ASSET_PATH.'/js/vue-router.min.js', array( 'woopo-vue' ), false, true );
        wp_enqueue_script('woopo-vuex', WOOPO_ASSET_PATH.'/js/vuex.js', array( 'woopo-vue' ), false, true );
        wp_enqueue_script('woopo-functions', WOOPO_ASSET_PATH.'/js/functions.js' );
        wp_enqueue_script('woopo-formbuilder-js', WOOPO_ASSET_PATH.'/js/templates/core/form-builder.js', array( 'woopo-vue' ), false, true );
        wp_enqueue_script('woopo-element-js', WOOPO_ASSET_PATH.'/js/element.js', array( 'woopo-vue' ), false, true );
        wp_enqueue_script('woopo-element-en-js', WOOPO_ASSET_PATH.'/js/element-en.js', array( 'woopo-vue' ), false, true );

        /*form data*/
        do_action('woopo_prepend_scripts_styles' );

        wp_enqueue_script('woopo-form-type-data-js', WOOPO_ASSET_PATH.'/js/form-type-data.js', array( 'woopo-vue' ), false, true );
        wp_enqueue_script('woopo-form-fields-js', WOOPO_ASSET_PATH.'/js/form-fields.js', array( 'woopo-vue' ), false, true );
        
        wp_enqueue_script('woopo-field-attributes-js', WOOPO_ASSET_PATH.'/js/field-attributes.js', array( 'woopo-vue' ), false, true );
        wp_localize_script( 'woopo-field-attributes-js', 'woopo_obj', array(
        	'post_types' => get_post_types(array(
        		'public' => true
	        )),
	        'post_statuses' => get_post_statuses(),
	        'post_comment_statuses' => array( 'open' => __( 'Open', 'woopo' ), 'close' => __( 'Close', 'woopo' ) )
        ));

        wp_enqueue_script('woopo-form-settings-js', WOOPO_ASSET_PATH.'/js/form-settings.js', array( 'woopo-vue' ), false, true );

	    $formats = get_theme_support( 'post-formats' );
	    $all_formats = array();
	    foreach ( $formats as $k => $format_data ) {
		    $all_formats = array_merge( $all_formats, $format_data );
	    }
        wp_localize_script( 'woopo-form-settings-js', 'woopo_obj', array(
		    'post_types' => get_post_types(array(
			    'public' => true
		    )),
		    'post_statuses' => get_post_statuses(),
		    'post_comment_statuses' => array( 'open' => __( 'Open', 'woopo' ), 'close' => __( 'Close', 'woopo' ) ),
	        'post_formats' => $all_formats,
	        'default_tax_category' => array_column(get_terms( array(
		        'taxonomy' => 'category',
		        'hide_empty' => false,
	        ) ), 'name', 'term_id' )
	    ));



        wp_enqueue_script('woopo-store-js', WOOPO_ASSET_PATH.'/js/store.js', array( 'woopo-vue' ), false, true );

        wp_enqueue_script('woopo-form-js', WOOPO_ASSET_PATH.'/js/templates/form.js', array( 'woopo-vue' ), false, true );

        do_action('woopo_load_scripts_styles' );

        wp_enqueue_script('woopo-script-js', WOOPO_ASSET_PATH.'/js/script.js', array( 'woopo-vue', 'jquery' ), false, true );
    }

    public function form_type_data() {
    	//include_once ''
    }

    public function prepend_scripts_styles() {
        if( !Woopo_Functions::is_pro() ) {
            wp_enqueue_script('woopo-pro-data-js', WOOPO_ASSET_PATH.'/js/pro-data.js'/*, array( 'woopo-vue' ), false, true */);
        }
    }

    public function admin_footer() {
        include_once 'templates/core/form-builder.php';
    }
}

Woopo_Admin::get_instance();