<?php

if( !class_exists( 'Woopo_Public_Single_Product' ) ) {
	class Woopo_Public_Single_Product {

		protected $submission_process, $item_form_labels = array();

		public function __construct() {
			add_action( 'woocommerce_before_variations_form', array( $this, 'add_personalized_name_field' ) );
			add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'add_personalized_name_field' ) );//

			add_action( 'woocommerce_add_to_cart_validation', array( $this, 'field_validation' ), 10, 3 );

			//add custom meta to cart line items
			add_action( 'woocommerce_add_cart_item_data', array( $this, 'add_cart_item_data' ), 10, 2 );

			//render custom meta on cart and checkout
			add_filter( 'woocommerce_get_item_data', array( $this, 'render_meta_on_cart_and_checkout' ), 10, 2 );

			//add custom meta to order line items
			add_action( 'woocommerce_add_order_item_meta', array( $this, 'hook_new_order_item_meta' ) ,1 ,4 );

			//
			add_filter( 'woocommerce_attribute_label', array($this, 'woo_attribute_label'), 10, 3 );
		}

		function add_personalized_name_field() {
			global $product;
			$group_id = Woopo_Functions::get_option_group($product->get_id());
			do_shortcode( '[woopo_form id="'.$group_id.'"]' );
		}

		function field_validation() {
			$this->submission_process = new Woopo_Submission_Process( $_POST );
			if( $this->submission_process->get_errors() ) {
				foreach ( $this->submission_process->get_errors() as $k => $error ) {
					wc_add_notice( __( $error, 'woopo' ), 'error' );
				}
				return false;
			};
			return true;
		}

		function add_cart_item_data( $cart_item_data, $product_id ) {
			//for personalized
			$cart_item_data['woopo_custom_data'] = array();
			$field_labels = $this->submission_process->get_field_labels();

			foreach ( $this->submission_process->get_form_fields() as $k => $field_name ) {
				if( isset( $_POST[$field_name] ) ) {
					$cart_item_data['woopo_custom_data'][] = array(
						'label' => isset( $field_labels[$field_name] ) ? $field_labels[$field_name] : '',
						'field_name' => $field_name,
						'value' => stripslashes( $_POST[$field_name] )
					);
				}
			}

			/* below statement make sure every add to cart action as unique line item */
			$cart_item_data['unique_key'] = md5( microtime().rand() );
			return $cart_item_data;
		}

		/**
		 * @param $cart_data
		 * @param null $cart_item
		 *
		 * @return array
		 */
		function render_meta_on_cart_and_checkout( $cart_data, $cart_item = null ) {

			$custom_items = array();
			/* Woo 2.4.2 updates */
			if( !empty( $cart_data ) ) {
				$custom_items = $cart_data;
			}

			if( isset( $cart_item['woopo_custom_data'] ) && is_array( $cart_item['woopo_custom_data'] ) ) {
				foreach ( $cart_item['woopo_custom_data'] as $k => $data ) {
					$custom_items[] = array( "name" => $data['label'], "value" => $data['value'] );
				}
			}

			return $custom_items;

		}

		/**
		 * @param $item
		 * @param $cart_item_key
		 * @param $values
		 * @param $order
		 */
		function hook_new_order_item_meta( $item_id, $values, $cart_item_key ) {
			if( isset( $values['woopo_custom_data'] ) && is_array( $values['woopo_custom_data'] ) ) {
				foreach ( $values['woopo_custom_data'] as $k => $data ) {
					wc_add_order_item_meta( $item_id, $data['field_name'], $data['value'] );
				}
			}
		}

		public function woo_attribute_label( $label, $key, $product ) {

			if( is_bool($product) ) return;

			if( !isset( $this->item_form_labels[$product->get_id()] ) ) {
				$this->item_form_labels[$product->get_id()] = Woopo_Functions::extract_form_labels( Woopo_Functions::get_option_group( $product->get_id() ) );
			}

			if( isset( $this->item_form_labels[$product->get_id()][$key] ) ) {
				$label = $this->item_form_labels[$product->get_id()][$key];
			}
			return $label;
		}
	}

	new Woopo_Public_Single_Product();
}