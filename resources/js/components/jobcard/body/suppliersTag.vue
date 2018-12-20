<template>
    
    <!-- Jobcard Suppliers Tag -->
    <Poptip word-wrap width="200" trigger="hover" content="Click to view Suppliers">
        <Tag type="border" color="green">+2 Suppliers</Tag>
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