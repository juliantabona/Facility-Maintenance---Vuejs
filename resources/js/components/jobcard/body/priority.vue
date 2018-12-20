<template>
    
    <!-- Jobcard Priority -->
    <span>
        <strong>Priority: </strong>
        <priorityTag :priorities="jobcard.priorities" :showEditBtn="true"></priorityTag>
    </span>

</template>
<script>
    import priorityTag from './../../priority/priorityTag.vue';

    export default {
        components: { priorityTag },
        props: [
            /*  Jobcard data  */
            'jobcard'
        ],
    };
</script>