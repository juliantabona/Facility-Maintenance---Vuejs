<template>
    
    <!-- Jobcard Created By -->
    <span>
        <strong>Created By: </strong>
        <Poptip word-wrap width="200" trigger="hover" :content="(jobcard.createdBy || {}).position">
            <span><a href="#">{{ (jobcard.createdBy || {}).fullName }}</a></span>
        </Poptip>
    </span>

</template>
<script>
    export default {
        props: [
            /*  Jobcard data  */
            'jobcard'
        ]
    };
</script>