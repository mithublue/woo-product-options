<script>
    /*global settings*/
    woopo_add_filter( 'woopo_global_settings', function( global_settings ) {
        var settings = {
            ///post submission
            //'edit_page' : '',
            //'default_post_author' : 'admin',
            //'default_post_form' : '',

            ///dashboard
            //'dashboard_page' : '',
            //'user_can_edit_post' : true,
            //'user_can_delete_post' : true,
            //'editable_post_status' : ['pending'],
            //'deletable_post_status' : ['pending'],

            //'dashboard_post_per_page' : 10,
            //'show_user_bio' : false,
            //'show_post_count' : false,
            //'show_featured_img' : false,
            //'unauth_msg' : 'You need to login to access this page',
            //login/reg
            //'login_page' : '',
            //'reg_page' : '',
            //'auto_login_after_reg' : false,
            //'login_redirect_page' : '',

        };
        global_settings = Object.assign({}, global_settings, settings );
        return global_settings;
    });
    woopo_add_filter( 'woopo_global_settings_sections', function (global_settings_sections) {

        var settings_sections = {
            general: {
                label: 'General',
                desc: 'General settings',
                schema: {
                    fields: []
                }
            }
        };

        global_settings_sections = Object.assign({},global_settings_sections, settings_sections);
        return global_settings_sections;
    });
    /**/
</script>