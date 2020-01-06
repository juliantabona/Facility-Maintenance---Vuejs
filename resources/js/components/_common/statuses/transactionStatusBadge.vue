<template>

    <!-- Main status badge -->
    <mainStatusBadge :status="localStatus"></mainStatusBadge>

</template>

<script type="text/javascript">

    import mainStatusBadge from './mainStatusBadge.vue';

    export default {
        props:{
            status: {
                type: Object,
                default: null
            }
        },
        components: { mainStatusBadge },
        data(){
            return {
                localStatus: this.getStatusInfo(this.status)
            }
        },
        watch: {
            status: {
                handler: function (val, oldVal) {
                    this.localStatus = this.getStatusInfo(val);
                },
                deep: true
            }
        },
        methods: {
            getStatusInfo(status){
                return {
                    name: status.name || '...',
                    description: status.description || '...',
                    color: this.determineStatusColor(status.name)
                }
            },
            determineStatusColor(name) {

                if(['Failed', 'Cancelled'].includes(name)) {
                    return 'error';
                }else if(['Success'].includes(name)) {
                    return 'success';
                }else{
                    return '';
                }

            }
        }
    }
</script>
