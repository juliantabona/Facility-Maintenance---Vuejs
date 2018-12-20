<template>
    
    <!-- Jobcard Labour Tag -->
    <Poptip word-wrap width="200" trigger="hover" content="Click to view Labour">
        <Tag type="border" color="green">+1 Labour</Tag>
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