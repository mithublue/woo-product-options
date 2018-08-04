var formFields = [
    //text
    {
        type: 'input',
        inputType: 'text',
        preview: {
            'label': 'Text',
            name: 'text'
        },
        settings:{
            atts: {
                span: 24
            }
        }
    },
    //textarea
    {
        type: 'input',
        inputType: 'textarea',
        preview: {
            'label': 'Textarea',
            name: 'textarea'
        },
        settings:{
            atts: {
                span: 24
            }
        }
    },
    //select
    {
        type: 'input',
        inputType: 'select',
        preview: {
            'label': 'Select',
            name: 'select'
        },
        settings:{
            atts: {
                span: 24
            }
        }
    },
    //number
    {
        type: 'input',
        inputType: 'number',
        preview: {
            'label': 'Number',
            name: 'number'
        },
        settings:{
            atts: {
                span: 24
            }
        }
    },
    //radio
    {
        type: 'input',
        inputType: 'radio',
        preview: {
            label: 'Radio',
            name: 'radio'
        },
        settings:{
            atts: {
                span: 24
            }
        }
    },
    //checkbox
    {
        type: 'input',
        inputType: 'checkbox',
        preview: {
            'label': 'Checkbox',
            name: 'checkbox'
        },
        settings:{
            atts: {
                span: 24
            }
        }
    },
];

formFields = woopo_apply_filters( 'woopo_formFields', formFields );