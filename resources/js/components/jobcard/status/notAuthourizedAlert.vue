<template>

    <!-- Jobcard not authourized alert -->
    <Alert v-if="!(jobcard || {}).authourizedBy" type="warning" show-icon class="mt-2 mb-2">
        This jobcard is not yet authourized for processing. At this state it can only be authourized by
        management with permissions to do so. - 
        <span class="btn btn-link btn-sm p-0">Authourize</span>
        or
        <span class="btn btn-link btn-sm p-0">Request Authourization by management</span>
    </Alert>

</template>
<script>
    export default {
        props: {
            /*  Jobcard data  */
            jobcard: {
                type: Object,
                //  Props with type Object/Array must use a factory function to return the default value.
                default: () => {}
            }
        },
    };
</script>