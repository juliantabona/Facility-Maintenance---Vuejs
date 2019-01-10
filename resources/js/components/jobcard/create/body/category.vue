<template>
    
    
    <!-- Jobcard Category -->
    <el-form-item prop="categories">
        <strong>Category: </strong>
        <categoryTag :categories="formData.categories" @updated="updateChanges" :showEditBtn="true"></categoryTag>
    </el-form-item>

</template>
<script>
    import categoryTag from './../../../_common/category/categoryTag.vue';

    export default {
        components: { categoryTag },
        props: [
            /*  Form data  */
            'formData'
        ],
        methods: {
            updateChanges(newCategories){
                this.formData.categories = newCategories;
            }
        }
    };
</script>