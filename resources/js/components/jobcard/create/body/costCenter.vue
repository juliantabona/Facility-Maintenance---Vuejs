<template>
    
    <!-- Jobcard Cost Centers -->
    <span>
        <strong>Cost Centers: </strong>
        <costcenterTag :costcenters="formData.costcenters" @updated="updateChanges" :showEditBtn="true"></costcenterTag>
    </span>

</template>
<script>
    import costcenterTag from './../../../_common/costcenter/costcenterTag.vue';

    export default {
        components: { costcenterTag },
        props: [
            /*  Form data  */
            'formData'
        ],
        methods: {
            updateChanges(newCostCenters){            
                this.formData.costCenters = newCostCenters;
            }
        }
    };
</script>