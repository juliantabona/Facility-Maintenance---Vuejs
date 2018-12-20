<template>
    
    <!-- Jobcard Authorized By -->
    <span>
        <strong>Authorized By: </strong>
        <Poptip word-wrap width="200" trigger="hover" :content="(jobcard.authourizedBy || {}).position">
            <span><a href="#">{{ (jobcard.authourizedBy || {}).fullName }}</a></span>
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