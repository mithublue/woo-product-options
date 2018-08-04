<template id="woopo-form-types">
    <div class="woopo-form-types">
        <?php $form_typs = Woopo_Functions::get_form_types(); ?>
	    <?php
	    $i = 0;
	    foreach ( $form_typs as $name => $val ) {
		    if( $i%3 == 0 ) {
			    ?>
                <el-row :gutter="12">
			    <?php
		    }
		    ?>
            <el-col :span="8" class="mb20">
                <el-card :body-style="{ padding: '0px' }" class="woopo_form_type">
                    <div style="padding: 14px;">
                        <h4><?php echo $val['label']; ?></h4>
                        <h5><?php echo isset($val['subtitle']) ? $val['subtitle'] : ''; ?></h5>
                        <div class="bottom clearfix">
                            <div class="mb30"><?php echo $val['desc']; ?></div>
						    <?php  if( !isset( $val['pro'] ) || !$val['pro'] ) { ?>
                                <?php
                                if( isset( $val['type'] ) ) { ?>
                                    <a class="el-button el-button--success form-create" href="#/forms/new-form/type/<?php echo $val['type']; ?>/<?php echo $name; ?>"><?php _e( 'Create', 'woopo' ); ?></a>
                                <?php } else { ?>
                                    <a class="el-button el-button--success form-create" href="#/forms/new-form/<?php echo $name; ?>"><?php _e( 'Create', 'woopo' ); ?></a>
                                <?php } ?>
						    <?php } ?>
						    <?php
						    if( isset( $val['url'] ) ) { ?>
                                <a class="el-button el-button--primary" target="_blank" href="<?php echo $val['url']; ?>"><?php _e( 'Get '.$val['label'].' now', 'woopo' ); ?></a>
						    <?php } ?>
                        </div>
                    </div>
                </el-card>
            </el-col>
		    <?php
		    $i++;
		    if( $i%3 == 0 ) {
			    ?>
                </el-row>
			    <?php
		    }
	    }
	    ?>
    </div>
</template>
<script>
    var woopo_form_types = {
        template: '#woopo-form-types'
    }
</script>