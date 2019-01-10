<template>
    
    <!-- Company Menu Card Slot -->
    <span>

        <!-- Company verified badge -->
        
        <authourizedBadge v-if="showAuthourizedStatus" v-bind="$props"></authourizedBadge>

        <!-- Company menu toggler -->
        
        <menuToggle v-if="showMenuBtn" v-bind="$props"></menuToggle>

    </span>

</template>
<script>

    import authourizedBadge from './authourizedBadge.vue';
    import menuToggle from './menuToggle.vue';

    export default {
        props: [
            /*  Company data  */
            'company',
            /*  Menu related properties  */
            'showMenuBtn', 'showAuthourizedStatus'
        ],
        components: { authourizedBadge, menuToggle }
    };
</script>