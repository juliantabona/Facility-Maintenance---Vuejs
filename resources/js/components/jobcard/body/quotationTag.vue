<template>
    
    <!-- Jobcard Quotation Tag -->
    <Poptip word-wrap width="200" trigger="hover" content="Click to view Quotation">
        <Tag type="border" color="green">+1 Quotation</Tag>
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