<?php
class Woopo_Ajax_actions {

    public static function init() {
        add_action( 'wp_ajax_woopo_update_form', array( __CLASS__, 'woopo_update_form' ) );


        add_action( 'wp_ajax_woopo_get_forms', array( __CLASS__, 'woopo_get_forms' ) );
        add_action( 'wp_ajax_woopo_get_form', array( __CLASS__, 'woopo_get_form' ) );
        add_action( 'wp_ajax_woopo_delete_form', array( __CLASS__, 'woopo_delete_form' ) );

        add_action( 'wp_ajax_woopo_save_global_settings', array( __CLASS__, 'save_global_settings' ) );
        add_action( 'wp_ajax_woopo_get_global_settings', array( __CLASS__, 'get_global_settings' ) );
        add_action( 'wp_ajax_woopo_recaptcha_validate', array( __CLASS__, 'recaptcha_validate' ) );

        //
	    add_action( 'wp_ajax_woopo_populate_form_type_data', array( __CLASS__, 'populate_form_type_data' ) );
	    add_action( 'wp_ajax_woopo_get_tax_terms', array( __CLASS__, 'get_tax_terms' ) );

	    add_action( 'wp_ajax_cc_get_news', array( __CLASS__, 'cc_get_news' ) );
	    add_action( 'wp_ajax_sm_dissmiss_news_notice', array( __CLASS__, 'dissmiss_news_notice' ) );
    }

    public static function woopo_update_form() {
        if( !current_user_can(Woopo_Functions::form_capability()) ) wp_send_json_error(array(
            'msg' => 'You are not allowed to apply this operation !'
        ));

        !isset( $_POST['status'] ) || !$_POST['status'] ? $_POST['status'] = 'draft' : '';
        !isset( $_POST['title'] ) || !$_POST['title'] ? $_POST['title'] = 'Product Option Group' : '';

        $title = sanitize_text_field( $_POST['title'] );
        $status = sanitize_text_field( $_POST['status'] );
        $formdata = wp_filter_post_kses($_POST['formdata']);
        $form_settings = wp_filter_post_kses($_POST['form_settings']);
	    $form_settings_decoded = json_decode( base64_decode($form_settings), true );

        if( isset( $formdata ) && $formdata ) {
            $arg = array(
                'post_title' => $title,
                'post_type' => 'woopo_form',
                'post_status' => $status,
                'meta_input' => array(
                    'woopo_formdata' => $formdata,
                    'woopo_form_settings' => $form_settings,
	                '_woopo_form_post_type' => isset( $form_settings_decoded['form_settings']['s']['post_type'] ) ? $form_settings_decoded['form_settings']['s']['post_type'] : ''
                )
            );

            //edit form
            if ( is_numeric( $_POST['form_id'] ) && $_POST['form_id'] ) {
                $arg['ID'] = $_POST['form_id'];
            }

            $result = wp_insert_post($arg);

            if( $result ) {
                wp_send_json_success(array(
                    'id' => $result
                ));
            }

            wp_send_json_error();
        }
        exit;
    }




    /**
     * Get forms
     */
    public static function woopo_get_forms() {

        if( !current_user_can(Woopo_Functions::form_capability()) ) wp_send_json_error(array(
            'msg' => 'You are not allowed to apply this operation !'
        ));

        if( !isset( $_POST['page'] ) || !$_POST['page'] || !is_numeric( $_POST['page'] ) ) {
            $_POST['page'] = 1;
        }


        if ( !isset( $_POST['status'] ) || !$_POST['status'] ) {
            $_POST['status'] = 'publish';
        }

        $page = $_POST['page'];
        $status = sanitize_text_field($_POST['status']);

        $forms = get_posts(array(
            'post_type' => 'woopo_form',
            'post_status' => $status,
            'posts_per_page' => 10,
            'offset' => ($page - 1)*10,
            'order' => 'DESC',
            'orderby' => 'ID'
        ));

        wp_send_json_success(array(
            'forms' => $forms,
            'counts' => wp_count_posts()->publish
        ));
    }

    /**
     * Get single form
     */
    public static function woopo_get_form() {

        if( !current_user_can(Woopo_Functions::form_capability()) ) wp_send_json_error(array(
            'msg' => 'You are not allowed to apply this operation !'
        ));

        if( !isset($_POST['id']) || !$_POST['id'] || !is_numeric($_POST['id']) ) wp_send_json_error();

        $id = $_POST['id'];
        $form = get_post($id);

        if ( $form ) {
            wp_send_json_success(array(
                'form' => $form,
                'formdata' => Woopo_Functions::get_formdata($id),
                'form_settings' => Woopo_Functions::get_form_settings($id)
            ));
        }
        wp_send_json_error();
    }

    /**
     * Delete form
     */
    public static function woopo_delete_form() {
        if( !current_user_can(Woopo_Functions::form_capability()) ) wp_send_json_error(array(
            'msg' => 'You are not allowed to apply this operation !'
        ));

        if( !isset($_POST['form_id']) || !$_POST['form_id'] || !is_numeric($_POST['form_id']) ) wp_send_json_error();
        $form_id = $_POST['form_id'];
        $result = '';

        if( isset( $_POST['trashDelete'] ) ) {
            if( $_POST['trashDelete'] ) {
                $result = wp_trash_post( $form_id );
            } else {
                $result = wp_delete_post( $form_id, true );
            }
        } else {
            $result = wp_delete_post( $form_id, true );
        }


        if( $result ) {
            wp_send_json_success(array(
                'msg' => 'Form Deleted !'
            ));
        }

        wp_send_json_error(array(
            'msg' => 'Form could not be deleted !'
        ));
    }

    /**
     * Save global settings
     */
    public static function save_global_settings () {
        $global_settings = wp_filter_post_kses($_POST['global_settings']);
        if( update_option( 'woopo_global_settings', $global_settings ) )
            wp_send_json_success(
                array(
                    'msg' => __( 'Data saved successfully', 'woopo' )
                )
            );
        wp_send_json_error(
            array(
                'msg' => __( 'Data could not be saved !', 'woopo' )
            )
        );
    }

    /**
     * Get global settings
     */
    public static function get_global_settings () {
        $global_settings =  get_option( 'woopo_global_settings' );
        if( $global_settings )
            wp_send_json_success(array(
                'global_settings' => $global_settings
            ));
        wp_send_json_error();
    }

    /**
     * Submitting the form
     */
    public static function submit_form() {

    }

	/**
	 * Populate form type data
	 * in form
	 */
    public static function populate_form_type_data() {
    	if( !isset( $_POST['type'] ) || !isset( $_POST['form_type'] ) ) wp_send_json_error();

    	$type = sanitize_text_field( $_POST['type'] );
    	$form_type = sanitize_text_field( $_POST['form_type'] );
	    $form_type_data_object = new Woopo_Form_Type_Data( $type, $form_type );
	    $form_type_data = $form_type_data_object->get_form_type_data();
	    $form_settings = $form_type_data_object->get_form_settings();

	    if( $form_type_data ) {
	    	wp_send_json_success(array(
	    		'form_type_data' => $form_type_data,
			    'form_settings' => $form_settings
		    ));
	    }

	    wp_send_json_error();
    }

    /**
     * Recaptcha Validation
     */
    public function recaptcha_validate() {
        if( !isset( $_POST['token'] ) ) wp_send_json_error();
        $token = $_POST['token'];
	    Woopo_Functions::recaptcha_validate( $token );
    }

	/**
	 * get all tax and terms of all post types
	 */
    public static function get_tax_terms() {
	    $post_types = get_post_types();

	    foreach ( $post_types as $post_type ) {
		    $taxonomy_names = get_object_taxonomies( $post_type );

		    $terms = get_terms( $taxonomy_names, array( 'hide_empty' => false ));
		    if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
			    echo '<h5>'.$post_type.'</h5>';
			    echo '<ul>';
			    foreach ( $terms as $term ) {
				    echo '<li>' . $term->name . '</li>';

			    }
			    echo '</ul>';
		    }
	    }
    }

    public static function cc_get_news() {
	    $response = wp_remote_get( 'http://blog.cybercraftit.com/api/get_category_posts?slug=news&count=10' );
	    $response = $response['body'];
	    $response = json_decode($response,true);
	    wp_send_json_success(array(
	    	'response' => $response
	    ));
    }

	public static function dissmiss_news_notice() {
		$notices = sm_get_notice('sm_admin_notices' );
		$notices['news_notice']['is_dismissed'] = true;
		if( isset( $_POST['last_news_date'] ) && is_numeric( $_POST['last_news_date'] ) ) {
			$notices['news_notice']['last_news_date'] = $_POST['last_news_date'];
		} else {
			$notices['news_notice']['last_news_date'] = 0;
		}

		if ( update_option( 'sm_admin_notices', $notices ) ) {
			wp_send_json_success();
		}
		exit;
	}
}

Woopo_Ajax_actions::init();