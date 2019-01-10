<template>
    
    <!-- Company Contacts Tag -->
    <span>
        <Poptip word-wrap width="200" trigger="hover" content="Click to view Contacts">
            <Tag type="border" color="green">+2 Contacts</Tag>
        </Poptip>
        <Button icon="ios-add" type="dashed" size="small">Add</Button>
    </span>

</template>
<script>
    export default {
        props: [
            /*  Company data  */
            'company'
        ],
    };
</script>