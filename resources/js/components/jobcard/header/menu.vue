<template>
    
    <!-- Jobcard Menu Card Slot -->
    <span>
        
        <!-- Jobcard authourized badge -->
        
        <authourizedBadge v-if="showAuthourizedStatus" v-bind="$props"></authourizedBadge>

        <!-- Jobcard status -->

        <lifecycleStatusTag v-if="showProcessStatus && jobcard.statusSummary" v-bind="$props"></lifecycleStatusTag>

        <!-- Jobcard menu toggler -->
        
        <menuToggle v-if="showMenuBtn" v-bind="$props"></menuToggle>

    </span>

</template>
<script>

    import authourizedBadge from './authourizedBadge.vue';
    import lifecycleStatusTag from './../lifecycle/lifecycleStatusTag.vue';
    import menuToggle from './menuToggle.vue';

    export default {
        props: [
            /*  Jobcard data  */
            'jobcard',
            /*  Menu related properties  */
            'showMenuBtn', 'showAuthourizedStatus', 'showProcessStatus'
        ],
        components: { authourizedBadge, lifecycleStatusTag, menuToggle },
        computed:{
            statusSummary: function(){
                return (this.jobcard.statusSummary || {});
            }
        }
    };
</script>