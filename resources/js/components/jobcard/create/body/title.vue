<template>
    
    <!-- Jobcard Title -->
    <el-form-item label="Title" prop="title">
        <el-input v-model="formData.title" size="small" style="width:100%"></el-input>
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