<?php

trait Woopo_Form {

	public function form_init() {
		add_shortcode( 'woopo_form', array( $this, 'form_definition') );
		add_action( 'wp_footer', array( $this, 'form_wp_footer'));
	}

	/**
	 * @param $atts
	 * @param $content
	 * @param $tags
	 */
	public function form_definition( $atts, $content, $tags ) {
		$atts = shortcode_atts( array(
			'id' => ''
		), $atts );

		if( !isset( $atts['id'] ) ) return;

		$form = get_post( $atts['id'] );
		if( !$form ) return;
		if( get_post_status($form->ID) !== 'publish' ) return;

		$this->form_settings[$atts['id']] = json_decode( base64_decode( Woopo_Functions::get_form_settings($atts['id']) ), true ) ;
		$form_settings = $this->form_settings[$atts['id']];

		/**
		 * Form restriction
		 */
		$is_restricted = 0;

		if( $form_settings['form_restriction']['s']['is_scheduled'] ) {
			if( $form_settings['form_restriction']['s']['schedule_from'] && $form_settings['form_restriction']['s']['schedule_to'] ) {
				if( time() < $form_settings['form_restriction']['s']['schedule_from'] || time() > strtotime( $form_settings['form_restriction']['s']['schedule_to'] ) ) {
					$is_restricted = 1;
					echo $form_settings['form_restriction']['s']['msg_before_schedule'];
				}
			}
		}

		if( $form_settings['form_restriction']['s']['limit_submission'] ) {
			if( $form_settings['form_restriction']['s']['number_of_submission'] <= Woopo_Functions::get_submission_occurance( $atts['id'] ) ) {
				$is_restricted = 1;
				echo $form_settings['form_restriction']['s']['limit_break_msg'];
			}
		};

		if( !$form_settings['form_restriction']['s']['guest_post'] ) {
			if( !is_user_logged_in() ) {
				$is_restricted = 1;
				echo $form_settings['form_restriction']['s']['require_login_msg'];
			}
		}

		$is_restricted = apply_filters( 'woopo_process_form_restriction', $is_restricted, $form_settings );

		if( $is_restricted ) {
			return;
		}

		$this->formdata[$atts['id']] = apply_filters( 'woopo_public_formdata_'.$form_settings['form_settings']['s']['form_type'], Woopo_Functions::get_formdata($atts['id']), $form_settings );

		/**
		 * if edit
		 */
		$pid = '';

		if( isset( $_GET['pid'] ) && is_numeric( $_GET['pid'] ) ) {
			$pid = $_GET['pid'];
			$this->current_post = get_post( $pid );

			//post found
			if( $this->current_post ) {
				$this->formdata[$atts['id']] = $this->populate_post_formdata( $pid, $this->formdata[$atts['id']], $this->form_settings[$atts['id']] );
			}
		}
		?>
		<div id="woopo-<?php echo $atts['id']; ?>">
			<?php do_action( 'woopo_before_form'); ?>
			<div class="mb10">
				<template v-for="(error,fieldname) in errors">
					<div class="mb5">
						<el-alert
							:title="error"
							type="error"
							:closable="false"
						>
						</el-alert>
					</div>
				</template>
				<template v-for="(success,fieldname) in successes">
					<el-alert
						:title="success"
						type="success"
						:closable="false"
					>
					</el-alert>
				</template>
			</div>
			<?php do_action( 'woopo_public_before_form', $this->formdata[$atts['id']], $this->form_settings[$atts['id']] ); ?>
			<div :class="'woopo_layout_' + form_settings.appearance_settings.s.layout_type">
				<?php do_action( 'woopo_form_start', $this->formdata[$atts['id']], $this->form_settings[$atts['id']] ); ?>
                <input type="hidden" name="woopo_submission_id" value="<?php echo $atts['id']; ?>">
                <input type="hidden" name="post_id" value="<?php echo $pid; ?>">
                <div>
                    <template v-if="!form_settings.form_settings.s.is_multistep || typeof form_settings.form_settings.s.is_multistep === 'undefined'">
                        <template v-for="(row_data,k) in formdata.field_data">
                            <woopo_row :relations="relations" v-if="row_data.type == 'row'" :row_number="k" :form_data="row_data.row_formdata"></woopo_row>
                        </template>
                    </template>
                </div>
				<?php do_action( 'woopo_form_end', $this->formdata[$atts['id']], $this->form_settings[$atts['id']] ); ?>
			</div>
			<?php do_action( 'woopo_after_form', $this->formdata[$atts['id']], $this->form_settings[$atts['id']] ); ?>
		</div>
		<?php
	}

	/**
	 * Footer code
	 */
	public function form_wp_footer() {
		global $post;
		if( !isset( $post->ID ) || !Woopo_Functions::get_option_group($post->ID) ) return;


		include_once WOOPO_ROOT.'/templates/form-public.php';

		foreach ( $this->formdata as $id => $formdata ) {
			?>
            <script>
                var app_element = '#' + 'woopo-' + '<?php echo $id; ?>';
                var ajaxURL = '<?php echo admin_url('admin-ajax.php'); ?>';
                ;document.addEventListener("DOMContentLoaded", function(event) {
                    new Vue({
                        el: app_element,
                        data: {
                            formdata: {},
                            form_settings: {},
                            errors: [],
                            successes: []
                        },
                        methods: {
                            process_form: function ( save_type ) {
                                var _this = this;

                                ;(function ($) {
                                    $.post(
                                        ajaxURL,
                                        {
                                            action: 'woopo_submit_form',
                                            formData: $(app_element + ' form').serialize(),
                                            save_type: save_type
                                        },
                                        function (data) {
                                            if( !data.success ) {
                                                _this.successes = [];
                                                _this.errors = data.data.errors;
                                            } else {
                                                _this.successes = [_this.form_settings.form_settings.s.success_msg];
                                                _this.errors = [];

                                                if( _this.form_settings.form_settings.s.redirect_to === 'to_page' ) {
                                                    window.location = '<?php echo get_permalink( $this->form_settings[$id]['form_settings']['s']['page_id'] ); ?>';

                                                } else if ( _this.form_settings.form_settings.s.redirect_to === 'to_url' ) {
                                                    window.location = _this.form_settings.form_settings.s.url;
                                                }
                                            }
                                        }
                                    );
                                }(jQuery));
                            }
                        },
                        computed: {
                            row_name_data: function () {
                                var name_data = {};
                                for( var k in this.formdata.field_data ) {
                                    if ( this.formdata.field_data[k].type === 'row' ) {
                                        for( var r in this.formdata.field_data[k].row_formdata ) {
                                            if( typeof this.formdata.field_data[k].row_formdata[r].s.value !== 'undefined' ) {
                                                name_data[this.formdata.field_data[k].row_formdata[r].s.name] = this.formdata.field_data[k].row_formdata[r].s.value;
                                            } else if ( typeof this.formdata.field_data[k].row_formdata[r].s.sel_values !== 'undefined' ) {
                                                name_data[this.formdata.field_data[k].row_formdata[r].s.name] = this.formdata.field_data[k].row_formdata[r].s.sel_values;
                                            } else if ( typeof this.formdata.field_data[k].row_formdata[r].s.selected_val !== 'undefined' ) {
                                                name_data[this.formdata.field_data[k].row_formdata[r].s.name] = this.formdata.field_data[k].row_formdata[r].s.selected_val;
                                            }
                                        }
                                    }
                                }
                                return name_data;
                            },
                            step_name_data: function () {
                                var name_data = {};
                                for( var k in this.formdata.field_data ) {
                                    if ( this.formdata.field_data[k].type === 'step' ) {
                                        for( var s in this.formdata.field_data[k].step_formdata ) {
                                            if ( this.formdata.field_data[k].step_formdata[s].type === 'row' ) {
                                                for( var r in this.formdata.field_data[k].step_formdata[s].row_formdata ) {
                                                    if( typeof this.formdata.field_data[k].step_formdata[s].row_formdata[r].s.value !== 'undefined' ) {
                                                        name_data[this.formdata.field_data[k].step_formdata[s].row_formdata[r].s.name] = this.formdata.field_data[k].step_formdata[s].row_formdata[r].s.value;
                                                    } else if ( typeof this.formdata.field_data[k].step_formdata[s].row_formdata[r].s.sel_values !== 'undefined' ) {
                                                        name_data[this.formdata.field_data[k].step_formdata[s].row_formdata[r].s.name] = this.formdata.field_data[k].step_formdata[s].row_formdata[r].s.sel_values;
                                                    } else if ( typeof this.formdata.field_data[k].step_formdata[s].row_formdata[r].s.selected_val !== 'undefined' ) {
                                                        name_data[this.formdata.field_data[k].step_formdata[s].row_formdata[r].s.name] = this.formdata.field_data[k].step_formdata[s].row_formdata[r].s.selected_val;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }

                                return name_data;
                            },
                            name_data: function () {
                                if( this.form_settings.form_settings.s.is_multistep ) {
                                    return this.step_name_data;
                                } else {
                                    return this.row_name_data;
                                }
                            },
                            row_relations: function () {
                                var relations = {};
                                for( var k in this.formdata.field_data ) {
                                    if( this.formdata.field_data[k].type == 'row' ) {
                                        for( var r in this.formdata.field_data[k].row_formdata ) {
                                            if( this.formdata.field_data[k].row_formdata[r].s.has_relation ) {

                                                var condition = 1;

                                                for( var rel in this.formdata.field_data[k].row_formdata[r].s.relation ) {
                                                    var relation_type = '';
                                                    if( this.formdata.field_data[k].row_formdata[r].s.relation[rel].relation_type === 'or' ) {
                                                        relation_type = '||';
                                                    } else {
                                                        relation_type = '&&';
                                                    }

                                                    if ( typeof this.name_data[this.formdata.field_data[k].row_formdata[r].s.relation[rel].field] === 'object' ) {
                                                        condition = this.name_data[this.formdata.field_data[k].row_formdata[r].s.relation[rel].field].indexOf( this.formdata.field_data[k].row_formdata[r].s.relation[rel].value ) !== -1 ? true : false;
                                                    } else {
                                                        condition = this.name_data[this.formdata.field_data[k].row_formdata[r].s.relation[rel].field] === this.formdata.field_data[k].row_formdata[r].s.relation[rel].value;
                                                    }
                                                }
                                                relations[this.formdata.field_data[k].row_formdata[r].s.name] = condition;
                                            } else {
                                                relations[this.formdata.field_data[k].row_formdata[r].s.name] = 1;
                                            }
                                        }
                                    }
                                }
                                return relations;
                            },
                            step_relations: function () {
                                var relations = {};
                                for( var k in this.formdata.field_data ) {
                                    if( this.formdata.field_data[k].type == 'step' ) {
                                        for( var s in this.formdata.field_data[k].step_formdata ) {
                                            if( this.formdata.field_data[k].step_formdata[s].type == 'row' ) {
                                                for( var r in this.formdata.field_data[k].step_formdata[s].row_formdata ) {
                                                    if( this.formdata.field_data[k].step_formdata[s].row_formdata[r].s.has_relation ) {

                                                        var condition = 1;
                                                        for( var rel in this.formdata.field_data[k].step_formdata[s].row_formdata[r].s.relation ) {
                                                            var relation_type = '';
                                                            if( this.formdata.field_data[k].step_formdata[s].row_formdata[r].s.relation[rel].relation_type === 'or' ) {
                                                                relation_type = '||';
                                                            } else {
                                                                relation_type = '&&';
                                                            }

                                                            if ( typeof this.name_data[this.formdata.field_data[k].step_formdata[s].row_formdata[r].s.relation[rel].field] === 'object' ) {
                                                                condition = this.name_data[this.formdata.field_data[k].step_formdata[s].row_formdata[r].s.relation[rel].field].indexOf( this.formdata.field_data[k].step_formdata[s].row_formdata[r].s.relation[rel].value ) !== -1 ? true : false;
                                                            } else {
                                                                condition = this.name_data[this.formdata.field_data[k].step_formdata[s].row_formdata[r].s.relation[rel].field] === this.formdata.field_data[k].step_formdata[s].row_formdata[r].s.relation[rel].value;
                                                            }
                                                        }
                                                        relations[this.formdata.field_data[k].step_formdata[s].row_formdata[r].s.name] = condition;
                                                    } else {
                                                        relations[this.formdata.field_data[k].step_formdata[s].row_formdata[r].s.name] = 1;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                return relations;
                            },
                            relations: function () {
                                if( this.form_settings.form_settings.s.is_multistep ) {
                                    return this.step_relations;
                                } else {
                                    return this.row_relations;
                                }
                            }
                        },
                        created: function () {
                            this.formdata = woopo_parse('<?php echo $formdata; ?>');
                            this.form_settings = woopo_parse('<?php echo base64_encode( json_encode( $this->form_settings[$id] ) ); ?>');
                        }
                    });
                });
            </script>
			<?php
		}
	}
}