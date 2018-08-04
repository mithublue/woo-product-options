<template id="woopo_edit_form">
    <div>

    </div>
</template>
<script>
    var woopo_edit_form = {
        template: '#woopo_edit_form',
        computed: {
            form: function () {
                return this.$store.getters.form;
            }
        },
        mounted: function () {

        }
    }
</script>