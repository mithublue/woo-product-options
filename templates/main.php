<div class="wrap">
    <div id="woopo-app">
        <div class="mt20">
            <router-view></router-view>
        </div>
    </div>
</div>
<?php
include_once 'form-types.php';
include_once 'forms.php';
include_once 'form.php';
include_once 'edit-form.php';
include_once 'settings.php';
include_once 'help.php';
include_once 'promo-get-pro.php';
include_once 'news.php';
do_action('woopo_admin_templates' );
