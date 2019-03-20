<template>

    <span v-if="localCategory">
        <Poptip v-for="(category, i) in categories" word-wrap width="250" trigger="hover" 
                :key="i" :content="category.description">
            <Tag type="border" color="primary">{{ category.name }}</Tag>
        </Poptip>
    </span>

</template>
<script type="text/javascript">

    export default {
        props:{
            categories: {
                type: Array,
                default: null
            }
        },
        data(){
            return {
                localCategory: this.categories
            }
        },
        watch: {
            categories: {
                handler: function (val, oldVal) {
                    this.localCategory = val;
                },
                deep: true
            }
        }
    }
</script>
