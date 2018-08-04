var woopo_routes = [
    {path: '/', redirect: '/forms'},
    {path: '/forms', component: woopo_forms},
    {path: '/forms/:status/page/:page', component: woopo_forms},
    {path: '/forms/form-types', redirect: '/forms/new-form/woopo_form'},
    {path: '/forms/new-form/:form_type', component: woopo_new_form},

    {path: '/forms/new-form/type/:type/:form_type', component: woopo_new_form},

    {path: '/forms/form/:form_id/edit', component: woopo_new_form},
    {path: '/forms/new-form/update', component: woopo_new_form},
    {path: '/settings', component: woopo_settings},

    {path: '/help', component: woopo_help},
    {path: '/get-pro', component: woopo_get_pro},
    {path: '/cc-news', component: cc_news},
];

woopo_routes = woopo_apply_filters( 'woopo_routes', woopo_routes );

const router = new VueRouter({
    routes : woopo_routes,
    'linkActiveClass': 'active'
});

var row_data =  {
    type: 'row',
    preview: {
        label: 'Row'
    },
    row_formdata: []
};

var app = new Vue({
    store: store,
    router: router,
    el: '#woopo-app',
    data: {}
});