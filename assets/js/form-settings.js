var woopo_post_types = {};

for( k in woopo_obj.post_types ) {
    woopo_post_types[k] = woopo_obj.post_types[k];
}

var woopo_form_settings = {
    form_settings: {
        for: '',
        label: 'Option Group Settings',
        s: {},
        schema: {
            fields: []
        }
    },
    form_restriction: {
        for: '',
        label: 'Restriction Settings',
        s: {
            is_scheduled: false,
            schedule_from: '',
            schedule_to: '',
            msg_before_schedule: '',
            guest_post: true,
            guest_post_fields: ['email'],
            require_login_msg: ''
        },
        schema: {
            fields: [
                {
                    model: 'is_scheduled',
                    type: 'input',
                    inputType: 'checkbox',
                    desc: 'Check this if you want the users enabled to submit form or a time period.',
                    options: {
                        true: 'Schedule form for a time period'
                    }
                },
                {
                    model: 'schedule_from',
                    type: 'datetimepicker',
                    label: 'Schedule Start Date',
                    desc: 'The date when the form will be accessible from',
                    visible: function (model) {
                        return model.is_scheduled;
                    }
                },
                {
                    model: 'schedule_to',
                    type: 'datetimepicker',
                    label: 'Schedule End Date',
                    desc: 'The date after when the form will not be accessible and submission will not be valid',
                    visible: function (model) {
                        return model.is_scheduled;
                    }
                },
                {
                    model: 'msg_before_schedule',
                    type: 'textarea',
                    label: 'Message before/after Schedule',
                    desc: 'Text that will be displayed if user visits the form page before schedule starts or after schedule ends.',
                    visible: function (model) {
                        return model.is_scheduled;
                    }
                },
                {
                    model: 'guest_post',
                    type: 'input',
                    inputType: 'checkbox',
                    desc: 'Check this if you want non logged in customers to have the ability to fill the fields.',
                    options: {
                        true: 'Enable submission by non logged in customers'
                    }
                },
                {
                    model: 'require_login_msg',
                    type: 'textarea',
                    label: 'Login Message',
                    desc: 'Message to show non loggedin users',
                    visible: function (model) {
                        return !model.guest_post;
                    }
                }
            ]
        }
    },
    appearance_settings: {
        for: '',
        label: 'Appearance Settings',
        s: {
            layout_type: 'rounded'
        },
        schema: {
            fields:[
                {
                    model: 'layout_type',
                    type: 'select',
                    label: 'Layout Type',
                    desc: 'Select layout type',
                    options: {
                        rounded: 'Rounded',
                        cornered: 'Cornered'
                    }
                }
            ]
        }
    }
};

woopo_form_settings = woopo_apply_filters( 'woopo_form_settings', woopo_form_settings );