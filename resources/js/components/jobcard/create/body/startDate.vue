<template>
    
    <!-- Jobcard start date -->
    <el-form-item label="Start Date" prop="startDate">
        <el-date-picker v-model="formData.startDate" type="datetime" placeholder="Start Date/Time" style="width:100%" 
            format="yyyy-MM-dd HH:mm:ss" value-format="yyyy-MM-dd HH:mm:ss">
        </el-date-picker>
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