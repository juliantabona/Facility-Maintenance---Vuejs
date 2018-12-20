<template>
    <span>
        <span v-if="costcenters.length" v-for="costcenter in costcenters">
            <Poptip word-wrap width="200" trigger="hover" :content="costcenter.description">
                <Tag type="border" color="primary">{{ costcenter.name }}</Tag>
            </Poptip>
        </span>
        <Poptip v-if="showAddBtn" word-wrap width="200" trigger="hover" content="Add a cost center">
            <Button icon="ios-add" type="dashed" size="small">Add</Button>
        </Poptip>
    </span>
</template>
<script type="text/javascript">
    export default {
        props:{
            costcenters: {
                type: Array,
                default: () => []
            },
            showAddBtn: {
                type: Boolean,
                default: true
            }
        }
    }
</script>
