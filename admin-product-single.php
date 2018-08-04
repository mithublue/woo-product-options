<?php

if( !class_exists( 'Woopo_Admin_Product' ) ) {
	class Woopo_Admin_Product {

		public function __construct() {
			add_filter( 'woocommerce_product_data_tabs', array( $this, 'add_custom_admin_product_tab' ) );
			add_action( 'woocommerce_product_data_panels', array( $this, 'options_product_tab_content' ) );
			add_action( 'woocommerce_process_product_meta', array( $this, 'save_auction_option_field' )  );
			//add_action( 'woocommerce_product_write_panel_tabs', array( $this, 'add_custom_admin_product_tab' ) );
		}

		function add_custom_admin_product_tab( $tabs) {
			$tabs['woopo'] = array(
				'label'		=> __( 'Product Options', 'wauc' ),
				'target'	=> 'woopo_product_options',
			);
			return $tabs;
		}

		function options_product_tab_content() {
			global $post;
			$option_groups = get_posts(array(
				'post_type' => 'woopo_form',
				'post_status' => 'publish',
				'posts_per_page' => -1
			));
			$group_list = array();
			foreach ( $option_groups as $k => $group ) {
				$group_list[$group->ID] = $group->post_title;
			};
			?>
            <div id='woopo_product_options' class='panel woocommerce_options_panel'>
                <div class='options_group'>
					<?php

					do_action( 'woopo_options_product_tab_top');

					// Download Type
					woocommerce_wp_select(
						array( 'id' => 'woopo_option_group_form',
						       'label' => __( 'Option Group', 'wauc' ),
						       'desc_tip'		=> 'true',
						       'description' => sprintf( __( 'Select option group', 'wauc' ) ),
						       'options' => $group_list
						)
					);
					do_action( 'woopo_options_product_tab_bottom' ); ?>
            </div>
            </div>
			<?php
        }

        public function save_auction_option_field( $post_id ) {
		    if( !is_numeric( $_POST['woopo_option_group_form'] ) ) return;
		    Woopo_Functions::update_option_group( $post_id, $_POST['woopo_option_group_form'] );
        }
	}

	new Woopo_Admin_Product();
}
