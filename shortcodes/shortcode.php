<?php

class Woopo_Shortcode_Handler {

    protected $formdata = array();
    protected $form_settings = array();
    public $current_post;

    protected $render_shortcode;

    use Woopo_Form;

    public function __construct() {

        add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts_styles'));
        add_action( 'wp_head', array( $this, 'wp_head' ) );
        $this->form_init();
    }

	/**
     * Populate data from post to form fields
	 * @param $post_id
	 * @param $formdata
	 */
    public function populate_post_formdata( $post_id, $formdata, $form_settings ) {
        $formdata = json_decode(base64_decode( $formdata ), true );

        if( !$form_settings['form_settings']['s']['is_multistep'] ) {
	        foreach ( $formdata['field_data'] as $v => $data ) {
		        if( $data['type'] == 'row' ) {
			        foreach ( $data['row_formdata'] as $k => $col_data ) {
				        if( isset( $this->current_post->{$col_data['s']['name']} ) ) {
					        $formdata['field_data'][$v]['row_formdata'][$k]['s']['value'] = $this->current_post->{$col_data['s']['name']};
				        };
			        }
		        }
	        }
        } else {
	        $formdata = apply_filters( 'populate_post_form_data_multistep', $formdata, $form_settings, $this );
        }

	    return $formdata = base64_encode( json_encode( $formdata ) );
    }

    /**
     * Enqueue scripts
     */
    public function wp_enqueue_scripts_styles() {
        global $post;
	    if( !isset( $post->ID ) && !Woopo_Functions::get_option_group($post->ID) ) return;

        wp_enqueue_style('woopo-framework-css', WOOPO_ASSET_PATH.'/css/framework.css' );
        wp_enqueue_style('woopo-style-css', WOOPO_ASSET_PATH.'/css/style.css' );
        wp_enqueue_style('woopo-element-css', WOOPO_ASSET_PATH.'/css/element.css' );

        wp_enqueue_script('woopo-vue', WOOPO_ASSET_PATH.'/js/vue.js', array(), false, true );

        wp_enqueue_script('woopo-element-js', WOOPO_ASSET_PATH.'/js/element.js', array( 'woopo-vue' ), false, true );
        wp_enqueue_script('woopo-element-en-js', WOOPO_ASSET_PATH.'/js/element-en.js', array( 'woopo-vue' ), false, true );

        /*form data*/
        wp_enqueue_script('woopo-functions-js', WOOPO_ASSET_PATH.'/js/functions.js', array( 'woopo-vue' ), false, true );
        wp_enqueue_script('woopo-comp-public-form-js', WOOPO_ASSET_PATH.'/js/templates/form-public.js', array( 'woopo-vue' ), false, true );
        wp_localize_script( 'woopo-functions-js', 'woopo_object', array(
                'ajaxurl' => admin_url('admin-ajax.php')
        ));

        do_action('woopo_public_load_scripts_styles' );
    }


    public function wp_head() {
        global $post;
	    if( !isset( $post->ID ) || !Woopo_Functions::get_option_group($post->ID) ) return;
            ?>
            <script src='https://www.google.com/recaptcha/api.js'></script>
            <?php

    }


}

new Woopo_Shortcode_Handler();