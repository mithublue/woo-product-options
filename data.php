<script>
    /*global settings*/
    woopo_add_filter( 'woopo_global_settings', function( global_settings ) {
        var settings = {};
        global_settings = Object.assign({}, global_settings, settings );
        return global_settings;
    });
    woopo_add_filter( 'woopo_global_settings_sections', function (global_settings_sections) {

        var settings_sections = {};

        global_settings_sections = Object.assign({},global_settings_sections, settings_sections);
        return global_settings_sections;
    });
    /**/
</script>