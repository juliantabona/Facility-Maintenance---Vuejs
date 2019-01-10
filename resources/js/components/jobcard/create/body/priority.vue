<template>
    
    <!-- Jobcard Priority -->
    <el-form-item prop="priority">
        <strong>Priority: </strong>
        <priorityTag :priority="formData.priority" @updated="updateChanges" :showEditBtn="true"></priorityTag>
    </el-form-item>

</template>
<script>
    import priorityTag from './../../../_common/priority/priorityTag.vue';

    export default {
        components: { priorityTag },
        props: [
            /*  Form data  */
            'formData'
        ],
        methods: {
            updateChanges(newPriority){
                this.formData.priority = newPriority;
            }
        }
    };
</script>