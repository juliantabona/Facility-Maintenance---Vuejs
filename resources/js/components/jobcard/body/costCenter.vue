<template>
    
    <!-- Jobcard Cost Centers -->
    <span>
        <strong>Cost Centers: </strong>
        <costcenterTag :costcenters="jobcard.costcenters" :showAddBtn="true"></costcenterTag>
    </span>

</template>
<script>
    import costcenterTag from './../../_common/costcenter/costcenterTag.vue';

    export default {
        components: { costcenterTag },
        props: [
            /*  Jobcard data  */
            'jobcard'
        ],
    };
</script>