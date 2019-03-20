<template>

    <span v-if="localCostCenter">
        <Poptip v-for="(category, i) in costcenters" word-wrap width="250" trigger="hover" 
                :key="i" :content="category.description">
            <Tag type="border" color="primary">{{ category.name }}</Tag>
        </Poptip>
    </span>

</template>
<script type="text/javascript">

    export default {
        props:{
            costcenters: {
                type: Array,
                default: null
            }
        },
        data(){
            return {
                localCostCenter: this.costcenters
            }
        },
        watch: {
            costcenters: {
                handler: function (val, oldVal) {
                    this.localCostCenter = val;
                },
                deep: true
            }
        }
    }
</script>
