<template id="woopo_public_form">
    <!--if -->
</template>
<script>
    ;document.addEventListener("DOMContentLoaded", function(event) {
        Vue.component('woopo_public_form',{
            props: ['formdata','relations','form_settings'],
            template: '#woopo_public_form'
        });
    });
</script>
<template id="woopo_row">
    <div>
        <el-row>
            <template v-for="(field_data,k) in form_data">
                <woopo_input_template :relations="relations" v-if="field_data.type == 'input' || field_data.inputType == 'text'" :field_data="field_data" :target_row="row_number" :target_col="k"></woopo_input_template>
            </template>
        </el-row>
    </div>
</template>
<script>
    ;document.addEventListener("DOMContentLoaded", function(event) {
        Vue.component('woopo_row',{
            template: '#woopo_row',
            props: ['row_number','form_data','relations']
        });
    });
</script>
<template id="woopo_input_template">
    <el-col :md="field_data.settings.atts.span">
        <div v-if="relations[field_data.s.name]">
            <div class="el-form-item" :class="{'is-required': field_data.s.required}">
                <label class="el-form-item__label" v-if="field_data.s.label">{{ field_data.s.label }}</label>
                <div class="el-form-item__content">
                    <div class="el-input">
	                    <?php do_action('woopo_form_item_top' )?>
                        <?php include_once 'form-items.php';?>
	                    <?php do_action('woopo_form_item_bottom' )?>
                    </div>
                </div>
            </div>
        </div>
    </el-col>
</template>
<script>
    ;document.addEventListener("DOMContentLoaded", function(event) {
        Vue.component('woopo_input_template',{
            props: ['field_data','target_row','target_col','relations'],
            template: '#woopo_input_template'
        });
    });
</script>
<?php do_action('components_form_items' ); ?>