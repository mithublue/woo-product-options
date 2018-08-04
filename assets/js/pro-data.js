woopo_add_filter( 'woopo_form_settings', function (woopo_form_settings) {
    var form_settings_schema = [
        {
            model: '',
            type: 'input',
            inputType: 'checkbox',
            desc: 'Check this if you want the form to be multistep (Pro)',
            options: {
                true: 'Is Multistep (Pro)'
            },
            wrapperclass: 'pro_fields'
        }
    ];
    woopo_form_settings.form_settings.schema.fields = form_settings_schema.concat(woopo_form_settings.form_settings.schema.fields);


    woopo_form_settings.form_restriction.schema.fields.push({
        model: 'pro_1',
        type: 'input',
        inputType: 'checkbox',
        label: 'Applicable for All Roles (Pro)',
        desc: 'Applicable for All Roles (Pro)',
        options: {
            true: 'Applicable for All Roles'
        },
        wrapperclass: 'pro_fields'
    });
    woopo_form_settings.form_restriction.schema.fields.push({
        model: 'pro_2',
        type: 'input',
        inputType: 'checkbox',
        label: 'Chose Roles (Pro)',
        options: $all_roles,
        wrapperclass: 'pro_fields'
    });

    return woopo_form_settings;
});

/**
 * Form fields
 */
woopo_add_filter( 'woopo_formFields', function ( formFields ) {
    var pro_formFields = [
        //for woopo starts
        //radio_group
        {
            pro: 1,
            type: 'input',
            inputType: 'radio_group',
            preview: {
                label: 'Radio Group',
                name: 'radio_group'
            },
            settings:{
                atts: {
                    span: 24
                }
            }
        },
        //option_group
        /*{
            type: 'input',
            inputType: 'option_group',
            preview: {
                label: 'Option Group',
                name: 'option_group'
            },
            settings:{
                atts: {
                    span: 24
                }
            }
        },*/
        //checkbox group
        {
            pro: 1,
            type: 'input',
            inputType: 'checkbox_group',
            preview: {
                'label': 'Checkbox Group',
                name: 'checkbox_group'
            },
            settings:{
                atts: {
                    span: 24
                }
            }
        },
        //website url
        {
            pro: 1,
            type: 'input',
            inputType: 'website_url',
            preview: {
                label: 'Website Url',
                name: 'website_url'
            },
            settings:{
                atts: {
                    span: 24
                }
            }
        },
        //email address
        {
            pro: 1,
            type: 'input',
            inputType: 'email_address',
            preview: {
                'label': 'Email Address',
                name: 'email_address'
            },
            settings:{
                atts: {
                    span: 24
                }
            }
        },
        //password
        {
            pro: 1,
            type: 'input',
            inputType: 'password',
            preview: {
                'label': 'Password',
                name: 'password'
            },
            settings:{
                atts: {
                    span: 24
                }
            }
        },
        //hidden_field
        {
            pro: 1,
            type: 'input',
            inputType: 'hidden_field',
            preview: {
                'label': 'Hidden Field',
                name: 'hidden_field'
            },
            settings:{
                atts: {
                    span: 24
                }
            }
        },
        //for woopo ends
//multiselect pro
        {
            pro: 1,
            type: 'input',
            inputType: 'select',
            preview: {
                'label': 'Multi Select',
                name: 'multiselect'
            },
            settings:{
                atts: {
                    span: 24
                }
            }
        },
        //repeat_field pro
        {
            pro: 1,
            type: 'input',
            inputType: 'repeat_field',
            preview: {
                'label': 'Repeat Field',
                name: 'repeat_field'
            },
            settings:{
                atts: {
                    span: 24
                }
            }
        },
        //country_list pro
        {
            pro: 1,
            type: 'input',
            inputType: 'country_list',
            preview: {
                'label': 'Country List',
                name: 'country_list'
            },
            settings:{
                atts: {
                    span: 24
                }
            }
        },
        //address pro
        {
            pro: 1,
            type: 'input',
            inputType: 'address',
            preview: {
                'label': 'Address',
                name: 'address'
            },
            settings:{
                atts: {
                    span: 24
                }
            }
        },
        //switch pro
        {
            pro: 1,
            type: 'input',
            inputType: 'switch',
            preview: {
                'label': 'Switch',
                name: 'switch'
            },
            settings:{
                atts: {
                    span: 24
                }
            }
        },
        //slider pro
        {
            pro: 1,
            type: 'input',
            inputType: 'slider',
            preview: {
                'label': 'slider',
                name: 'slider'
            },
            settings:{
                atts: {
                    span: 24
                }
            }
        },
        //datetimepicker pro
        {
            pro: 1,
            type: 'input',
            inputType: 'datetimepicker',
            preview: {
                'label': 'Datetimepicker',
                name: 'datetimepicker'
            },
            settings:{
                atts: {
                    span: 24
                }
            }
        },
        //rate pro
        {
            pro: 1,
            type: 'input',
            inputType: 'rate',
            preview: {
                'label': 'Rate',
                name: 'rate'
            },
            settings:{
                atts: {
                    span: 24
                }
            }
        },
        //Google map pro
        {
            pro: 1,
            type: 'input',
            inputType: 'google_map',
            preview: {
                'label': 'Google Map',
                name: 'google_map'
            },
            settings:{
                atts: {
                    span: 24
                }
            }
        },
        //Custom HTML pro
        {
            pro: 1,
            type: 'input',
            inputType: 'custom_html',
            preview: {
                'label': 'Custom HTML',
                name: 'custom_html'
            },
            settings:{
                atts: {
                    span: 24
                }
            }
        },
        //upload
        {
            pro: 1,
            type: 'input',
            inputType: 'upload',
            preview: {
                'label': 'Upload',
                name: 'upload'
            },
            settings:{
                atts: {
                    span: 24
                }
            }
        },
        //colorpicker pro
        {
            pro: 1,
            type: 'input',
            inputType: 'colorpicker',
            preview: {
                'label': 'Color Picker',
                name: 'colorpicker'
            },
            settings:{
                atts: {
                    span: 24
                }
            }
        }
    ];
    formFields = formFields.concat(pro_formFields);
    return formFields;
} );


/**
 * global settings schema
 */
woopo_add_filter( 'woopo_global_settings_sections', function (global_settings_sections) {
    var settings_sections = {
        recaptcha: {
            label: 'Recaptcha (Pro)',
            desc: 'You can get your recaptcha credentials from https://www.google.com/recaptcha',
            schema: {
                fields: [
                    {
                        model: '',
                        type: 'input',
                        inputType: 'text',
                        label: 'Site Key (Pro)',
                        desc: 'Site key for recaptcha',
                        class: 'disabled',
                        wrapperclass: 'pro_fields'
                    },
                    {
                        model: '',
                        type: 'input',
                        inputType: 'text',
                        label: 'Secret Key (Pro)',
                        desc: 'Secret key for recaptcha',
                        wrapperclass: 'pro_fields'
                    }
                ]
            }
        },
        google_map: {
            label: 'Google Map (Pro)',
            desc: '',
            schema: {
                fields: [
                    {
                        model: '',
                        type: 'input',
                        inputType: 'text',
                        label: 'Google Map API Key (Pro)',
                        wrapperclass: 'pro_fields'
                    }
                ]
            }
        }
    };
    global_settings_sections = Object.assign({},global_settings_sections, settings_sections);
    return global_settings_sections;
});
