<template>
    <span>
        <span v-if="categories.length" v-for="(category, index) in categories" :key="index">
            <Poptip word-wrap width="200" trigger="hover" :content="category.description">
                <Tag type="border" color="primary">{{ category.name }}</Tag>
            </Poptip>
        </span>
        <Poptip v-if="showEditBtn && !categories.length" word-wrap width="200" trigger="hover" content="Add category">
            <Button icon="ios-add" type="dashed" size="small">Add</Button>
        </Poptip>
        <Poptip v-if="showEditBtn && categories.length" word-wrap width="200" trigger="hover" content="Change the category">
            <Button type="dashed" size="small"><Icon type="ios-create-outline" :size="18" class="mr-2"></Icon></Button>
        </Poptip>
    </span>
</template>
<script type="text/javascript">
    export default {
        props:{
            categories: {
                type: Array,
                default: () => []
            },
            showEditBtn: {
                type: Boolean,
                default: true
            }
        }
    }
</script>
