<?php

class Woopo_Submission_Process {

    /**
     * @var Singleton The reference the *Singleton* instance of this class
     */
    private static $instance;
    protected $postdata;
    protected $form_settings;
    protected $formdata;
    protected $returned_data = array();
	/**
	 * Grab all fields
	 * @var
	 */
    protected $form_fields = array();
    protected $errors = array();

	protected $field_labels = array();

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

    public function __construct( $postdata ) {

        /**
         * Check if the form exists
         * and published
         */
        if( Woopo_Functions::get_form_post_status( $postdata['woopo_submission_id'] ) !== 'publish' ) {
            $this->set_error( 'Form is not submittable !' );
            return false;
        }

        $this->postdata = $postdata;
        $this->form_settings = Woopo_Functions::get_form_settings($this->postdata['woopo_submission_id'], true );
        $this->formdata = Woopo_Functions::get_formdata($this->postdata['woopo_submission_id'], true );

        if( empty( $this->get_errors() ) ) {
            if( $this->process_form() ) {
	            /**
	             * Save number of submission
	             * in form
	             */
	            Woopo_Functions::update_submission_occurance( $this->postdata['woopo_submission_id'] );
	            return true;
            }
        }

        if( empty( $this->get_errors() ) )
            $this->set_error( 'Something went wrong !' );
        return false;
    }


    /**
     * Process form
     * @return bool|mixed|void
     */
    public function process_form () {
        /**
         * Data validation to check if
         * all data is okay
         */
        if( !$this->validate_data( $this->postdata, $this->formdata ) ) {
            return false;
        };

        if( !$this->common_validation() ) {
            return false;
        }

        return true;
    }

    /**
     * Form specific process
     * Send mail
     * @return bool
     */
    public function process_post_type() {
        $form_settings = $this->form_settings;
	    return true;
    }

	/**
	 * Common validation
	 * E.g: security check
	 * @return bool
	 */
    public function common_validation() {
        /**
         * Recaptcha validation
         */
        if( isset( $this->postdata['g-recaptcha-response'] ) ) {
            $token = $this->postdata['g-recaptcha-response'];
            if( Woopo_Functions::recaptcha_validate($token) ) {
                return true;
            } else {
                $this->set_error( 'Recaptcha is not valid' );
            }
            return false;
        }

        return true;
    }

    /**
     * Data validation after submission
     */
    public function validate_data() {
        $postdata = $this->postdata;
        $formdata = $this->formdata;

	    if( !$this->form_settings['form_settings']['s']['is_multistep'] ) {
		    foreach ( $formdata['field_data'] as $k => $data ) {
			    if( $data['type'] == 'row' ) {
				    $this->row_validation($data);
			    }
		    }
	    } else {
		    do_action( 'woopo_form_validate_data', $postdata, $formdata, $this );
	    }


        if( empty( $this->get_errors() ) )
            return true;
        return false;
    }

	public function row_validation( $data ) {
		$postdata = $this->postdata;

		foreach ( $data['row_formdata'] as $k => $col_data ) {
			/**
			 * Validation : Required
			 */
			if( isset( $col_data['s']['required'] ) && $col_data['s']['required'] == true ) {
				/**
				 * if data type if
				 * file
				 */
				//neoforms_pri($_FILES);
				if( $col_data['preview']['name'] == 'upload' ) {
					if( $_FILES[$col_data['s']['name']]['error'][0] ) {
						$this->set_error( ( isset( $col_data['s']['label'] ) ? $col_data['s']['label'] : $col_data['s']['name'] ) .' is Required ' );
					}
				} else {
					if( !isset( $postdata[$col_data['s']['name']] ) || empty( $postdata[$col_data['s']['name']] ) ) {
						$this->set_error( ( isset( $col_data['s']['label'] ) ? $col_data['s']['label'] : $col_data['s']['name'] ) .' is Required ' );
					}
				}
			}

			$this->form_fields[] = $col_data['s']['name'];
			$this->field_labels[$col_data['s']['name']] = $col_data['s']['label'];
		}
	}

    /**
     * Set Errors
     * @param $msg
     */
    public function set_error( $msg ) {
        $this->errors[] = $msg;
    }

    /**
     * Get Errors
     * @return array
     */
    public function get_errors() {
        return $this->errors;
    }

	/**
	 * @param $key
	 * @param $value
	 */
    public function set_returned_data( $key = '', $value = '' ) {
    	if ( is_array( $key ) ) {
		    $this->returned_data = array_merge( $this->returned_data, $key );
	    } else {
		    $this->returned_data[$key] = $value;
	    }
    }

    public function get_returned_data() {
    	return $this->returned_data;
    }

	public function get_form_fields() {
		return $this->form_fields;
	}

	public function get_field_labels() {
		return $this->field_labels;
	}

    public function get_postdata() {
    	return $this->postdata;
    }

}
