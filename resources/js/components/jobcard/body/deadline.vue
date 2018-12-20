<template>
    
    <!-- Jobcard Deadline -->
    <span>
        <strong>Deadline: </strong>{{ jobcard.deadlineInWords }}
    </span>

</template>
<script>
    export default {
        props: [
            /*  Jobcard data  */
            'jobcard'
        ],
    };
</script>