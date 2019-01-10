<template>
    
    <!-- Jobcard description -->
    <el-form-item label="Description" prop="description">
        <el-input type="textarea" v-model="formData.description"></el-input>
    </el-form-item>

</template>
<script>
    export default {
        props: [
            /*  Form data  */
            'formData'
        ],
    };
</script>