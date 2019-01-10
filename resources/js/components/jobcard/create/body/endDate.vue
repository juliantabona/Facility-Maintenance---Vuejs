<template>
    
    <!-- Jobcard end date -->
    <el-form-item label="End Date" prop="endDate">
        <el-date-picker v-model="formData.endDate" type="datetime" placeholder="End Date/Time" style="width:100%"
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