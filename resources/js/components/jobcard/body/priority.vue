<template>
    
    <!-- Jobcard Priority -->
    <span>
        <strong>Priority: </strong>
        <priorityTag :priority="jobcard.priority[0]" :showEditBtn="true"></priorityTag>
    </span>

</template>
<script>
    import priorityTag from './../../_common/priority/priorityTag.vue';

    export default {
        components: { priorityTag },
        props: [
            /*  Jobcard data  */
            'jobcard'
        ],
    };
</script>