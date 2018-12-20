<template>
    
    <!-- Jobcard Assets Tag -->
    <Poptip word-wrap width="200" trigger="hover" content="Click to view Assets">
        <Tag type="border" color="green">+3 Assets</Tag>
    </Poptip>

</template>
<script>
    export default {
        props: [
            /*  Jobcard data  */
            'jobcard'
        ],
    };
</script>