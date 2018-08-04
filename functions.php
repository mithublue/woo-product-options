<?php

if( !function_exists('woopo_pri' ) ) {
    function woopo_pri( $data ) {
        echo '<pre>'; print_r($data);echo '</pre>';
    }
}

if( !function_exists( 'sm_get_notice' ) ) {
	function sm_get_notice ( $notice_name =  'sm_admin_notices'  ) {
		$notice = get_option( $notice_name );
		if( !is_array( $notice ) ) $notice = array();
		return $notice;
	}
}

class Woopo_Functions {

    /**
     * Check if the plugin is pro
     * @return bool
     */
    static function is_pro() {
        if( is_file( dirname(__FILE__).'/pro/loader.php' ) ) {
            return true;
        }
        return false;
    }

    /**
     * get form capability
     * @return mixed|void
     */
    public static function form_capability() {
        return apply_filters( 'woopo_form_capability', 'manage_options');
    }

    /**
     * Get form types
     */
    public static function get_form_types() {
    	$post_types = get_post_types( array(
    		'public' => true
	    ), 'ARRAY_A');

	    $form_types = array();

	    foreach ( $post_types as $post_type => $each ) {
    		$form_types[$post_type] = array(
    			'type' => 'post_type',
    			'label' => isset( $each->labels->singular_name ) ? $each->labels->singular_name : $each->label,
			    'desc' => __( 'Choose this if you want to create '. ( isset( $each->labels->singular_name ) ? $each->labels->singular_name : $each->label ).' form' )
		    );
	    }
        return apply_filters( 'woopo_form_types', $form_types);
    }

    public static function get_form_post_status( $id ) {
        return get_post_status($id);
    }

    /**
     * Get formdata for
     * the given id
     * @param $id
     * @return mixed
     */
    public static function get_formdata( $id, $formatted = false ) {
        if( $formatted ) {
            return json_decode(base64_decode(get_post_meta( $id, 'woopo_formdata', true )), true);
        }
        return $formdata = get_post_meta( $id, 'woopo_formdata', true );
    }

    /**
     * Get form settings
     * for given id
     * @param $id
     * @return mixed
     */
    public static function get_form_settings( $id, $formatted = false ) {
        if( $formatted ) {
            return json_decode(base64_decode(get_post_meta( $id, 'woopo_form_settings', true ) ), true);
        }
        return $form_settings = get_post_meta( $id, 'woopo_form_settings', true );
    }

    /**
     * Get global settings
     * @param null $option_name
     * @return array|mixed|object|string|void
     */
    public static function get_settings($option_name = null) {
        global $woopo_global_settings;
        if( !$woopo_global_settings ) {
            $woopo_global_settings = get_option( 'woopo_global_settings' );
            $woopo_global_settings = json_decode(base64_decode($woopo_global_settings),true);
        }

        if( $option_name ) {
            return isset($woopo_global_settings[$option_name]) ? $woopo_global_settings[$option_name] : '';
        }

        return $woopo_global_settings;
    }

    /**
     * Validate recaptcha
     */
    public static function recaptcha_validate($token) {
        $response = wp_remote_post( 'https://www.google.com/recaptcha/api/siteverify?secret='.Woopo_Functions::get_settings('secrect_key').'&response='.$token );

        if( isset( $response['body'] ) ) {
            $body = json_decode($response['body'], true );
            if( $body['success'] ) {
                return true;
            }
        }
        return false;
    }

    public static function get_submission_occurance ( $submission_id ) {
	    $woopo_submission_times = get_post_meta( $submission_id, 'woopo_submission_times', true );
	    !$woopo_submission_times ? $woopo_submission_times = 0 : '';
	    return $woopo_submission_times;
    }
    /**
     * Update submission times
     */
    public static function update_submission_occurance( $submission_id, $occurance = 1 ) {
	    $woopo_submission_times = get_post_meta( $submission_id, 'woopo_submission_times', true );
	    if( !$woopo_submission_times ) $woopo_submission_times = 0;
	    update_post_meta( $submission_id, 'woopo_submission_times',$woopo_submission_times + $occurance );
    }

    public static function get_post_form( $post_id ) {
    	return get_post_meta( $post_id, '_woopo_submission_id', true );
    }

    public static function get_fallback_form() {
    	return Woopo_Functions::get_settings('default_post_form' );
    }

    public static function get_posts( $user_id, $post_types = array(), $post_statuses = array(), $paged = 0 ) {
    	$args = array(
    		'post_type' => $post_types,
		    'post_status' => $post_statuses,
		    'author' => $user_id,
		    'paged' => $paged
	    );
	    $the_query = new WP_Query( $args );

	    return $the_query;
    }

    public static function extract_form_labels( $form_id ) {
    	$field_labels = array();
	    $formdata = Woopo_Functions::get_formdata($form_id, true );
	    $form_settings = Woopo_Functions::get_form_settings($form_id, true );

	    if( !$form_settings['form_settings']['s']['is_multistep'] ) {
		    foreach ( $formdata['field_data'] as $k => $data ) {
			    if( $data['type'] == 'row' ) {
				    foreach ( $data['row_formdata'] as $k => $col_data ) {
					    $field_labels[$col_data['s']['name']] = $col_data['s']['label'];
				    }
			    }
		    }
	    } else {
		    $field_labels = apply_filters( 'woopo_extract_field_labels_data_multistep', $field_labels, $formdata, $form_settings );
	    }

	    return $field_labels;
    }

	/**
	 * Checkc users permission
	 * @param $action
	 * @param $scope
	 */
    public static function can_user( $action, $scope ) {
	    //if( current_user_can( 'administrator' ) ) return true;

    	switch ($scope) {
		    case 'post' :
			    switch ( $action ) {
				    case 'delete':
					    if( Woopo_Functions::get_settings('user_can_delete_post') ) return true;
					    break;
			    }
			    break;

	    }
	    return false;
    }

    public static function update_option_group( $product_id, $group_id ) {
	    update_post_meta( $product_id, 'woopo_option_group_form', $group_id );
    }

    public static function get_option_group( $product_id ) {
    	return get_post_meta( $product_id, 'woopo_option_group_form', true );
    }
}