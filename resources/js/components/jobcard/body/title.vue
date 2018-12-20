<template>
    
    <!-- Jobcard Title -->
    <span><strong>Title:</strong> {{ jobcard.title }}</span>

</template>
<script>
    export default {
        props: [
            /*  Jobcard data  */
            'jobcard'
        ],
    };
</script>