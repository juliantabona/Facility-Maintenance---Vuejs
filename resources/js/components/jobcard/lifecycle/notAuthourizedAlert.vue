<template>

    <!-- Jobcard not authourized alert -->
    <Alert v-if="!(jobcard || {}).authourizedBy" type="warning" show-icon class="mt-2 mb-2">
        This jobcard is not yet authourized for processing. At this state it can only be authourized by management with permissions to do so. - 
        <span class="btn btn-link btn-sm p-0">
            <Poptip word-wrap width="200" trigger="hover" content="Authourize this jobcard yourself">
                Authourize
            </Poptip>
        </span> 
        
        or 

        <span class="btn btn-link btn-sm p-0">
            <Poptip word-wrap width="200" trigger="hover" content="Ask management to authourize this jobcard">
                Request Authourization by management
            </Poptip>
        </span>
    </Alert>

</template>
<script>
    export default {
        props: {
            /*  Jobcard data  */
            jobcard: {
                type: Object,
                default: null
            }
        },
    };
</script>