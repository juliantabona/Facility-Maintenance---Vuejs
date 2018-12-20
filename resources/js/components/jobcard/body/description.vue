<template>
    
    <!-- Jobcard Description -->
    <span>
        <strong>Description: </strong>{{ jobcard.description }}
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