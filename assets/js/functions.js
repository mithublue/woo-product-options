var woopo_hooks = {};

function woopo_add_filter( filtername, anonymus_function ) {
    if( typeof woopo_hooks[filtername] === 'undefined' ) {
        woopo_hooks[filtername] = {};
    }

    woopo_hooks[filtername][Object.keys(woopo_hooks[filtername]).length] = anonymus_function;
}

function woopo_apply_filters (filtername, data) {
    if( typeof woopo_hooks[filtername] !== 'undefined' ) {
        for ( k in woopo_hooks[filtername] ) {
            data = woopo_hooks[filtername][k](data);
        }
    }
    return data;
}

if( typeof woopo_stringify !== 'function' ) {
    var woopo_stringify  = function (field) {
        var json = btoa(JSON.stringify(field, function(key, value) {
            if (typeof value === "function") {
                return "/Function(" + value.toString() + ")/";
            }
            return value;
        }));
        return json;
    };
}

if( typeof woopo_parse !== 'function' ) {
    var woopo_parse = function (json) {
        var field = JSON.parse(atob(json), function(key, value) {
            if (typeof value === "string" &&
                value.startsWith("/Function(") &&
                value.endsWith(")/")) {
                value = value.substring(10, value.length - 2);
                return eval("(" + value + ")");
            }
            return value;
        });
        return field;
    };
}

if( typeof woopo_reset_fields !== 'function' ) {
    var woopo_reset_fields = function (field) {

        // Convert to JSON using a replacer function to output
        // the string version of a function with /Function(
        // in front and )/ at the end.
        var json = JSON.stringify(field, function(key, value) {
            if (typeof value === "function") {
                return "/Function(" + value.toString() + ")/";
            }
            return value;
        });

        // Convert to an object using a reviver function that
        // recognizes the /Function(...)/ value and converts it
        // into a function via -shudder- `eval`.
        var field = JSON.parse(json, function(key, value) {
            if (typeof value === "string" &&
                value.startsWith("/Function(") &&
                value.endsWith(")/")) {
                value = value.substring(10, value.length - 2);
                return eval("(" + value + ")");
            }
            return value;
        });
        return field;
    };
}

function woopo_make_sortable_row(sortObj,item,self,self_items) {
    ;(function ($) {
        $(sortObj).sortable({
            items: item,
            handle: '.woopo_row_mover',
            update: function (event,ui) {

                self.render_field_list = false;

                var prev_index = jQuery(ui.item).attr('target_row');
                var current_index = jQuery(ui.item).index()-1;

                var temp_obj = woopo_reset_fields( self_items[prev_index] );
                self_items.splice(prev_index,1);

                setTimeout(function () {
                    self_items.splice(current_index,0,temp_obj);
                    self.render_field_list = true;

                    setTimeout(function () {
                        woopo_make_sortable_row(sortObj,item,self,self_items);
                        woopo_make_sortable_field('.ui-woopo_row','.ui-woopo_col',self,self_items);
                    },1);
                },1);
            }
        });
    }(jQuery));
}

function woopo_make_sortable_field(sortObj,item,self,self_items) {
    ;(function ($) {
        $(sortObj).sortable({
            items: item,
            handle: '.woopo_col_mover',
            connectWith: sortObj,
            update: function (event,ui) {

                self.render_field_list = false;

                var prev_index = $(ui.item).attr('target_col');
                var prev_row_index = $(ui.item).attr('target_row');

                var current_index = $(ui.item).index() - 1;
                var current_row_index = $(ui.item).closest('.ui-woopo_row').attr('target_row');

                var temp_obj = woopo_reset_fields( self_items[prev_row_index].row_formdata[prev_index] );
                
                self_items[prev_row_index].row_formdata.splice(prev_index,1);
                setTimeout(function () {
                    self_items[current_row_index].row_formdata.splice(current_index,0,temp_obj);
                    self.render_field_list = true;
                    setTimeout(function () {
                        woopo_make_sortable_row('#ui-woopo_builder_ground','.ui-woopo_row',self,self_items);
                        woopo_make_sortable_field(sortObj,item,self,self_items);
                    },1);
                },1);
            }
        });
    }(jQuery));
}

function woopo_reset_sortable( self, is_multistep ) {
    setTimeout(function () {
        if( !is_multistep ) {
            woopo_make_sortable_row('#ui-woopo_panel_ground','.ui-woopo_row',self,self.formdata.field_data);

            woopo_make_sortable_row('#ui-woopo_builder_ground','.ui-woopo_row',self,self.formdata.field_data);
            woopo_make_sortable_field('.ui-woopo_row','.ui-woopo_col',self,self.formdata.field_data);
        } else {
            for( var s in self.field_data ) {
                woopo_make_sortable_row('#ui-woopo_panel_ground','.ui-woopo_row',self,self.formdata.field_data[s].step_formdata);

                woopo_make_sortable_row('#ui-woopo_builder_ground','.ui-woopo_row',self,self.formdata.field_data[s].step_formdata);
                woopo_make_sortable_field('.ui-woopo_row','.ui-woopo_col',self,self.formdata.field_data[s].step_formdata);
            }
        }
    },1000);

}

function woopo_reset_sortable_row( self, is_multistep ) {
    if( !is_multistep ) {
        setTimeout(function () {
            woopo_make_sortable_row('#ui-woopo_panel_ground','.ui-woopo_row',self,self.formdata.field_data);

            woopo_make_sortable_row('#ui-woopo_builder_ground','.ui-woopo_row',self,self.formdata.field_data);
        },1);
    } else {
        for( var s in self.field_data ) {
            woopo_make_sortable_row('#ui-woopo_panel_ground','.ui-woopo_row',self,self.formdata.field_data[s].step_formdata);

            woopo_make_sortable_row('#ui-woopo_builder_ground','.ui-woopo_row',self,self.formdata.field_data[s].step_formdata);
        }
    }
}

function woopo_reset_sortable_field( self, is_multistep ) {
    if( !is_multistep ) {
        setTimeout(function () {
            woopo_make_sortable_field('.ui-woopo_row','.ui-woopo_col',self,self.formdata.field_data);
        },1);
    } else {
        for( var s in self.field_data ) {
            woopo_make_sortable_field('.ui-woopo_row','.ui-woopo_col',self,self.formdata.field_data[s].step_formdata);
        }
    }
}