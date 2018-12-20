<template>

    <!-- Jobcard Status -->
    <span v-if="jobcard.statusSummary">
        <strong v-if="showText">Status: </strong>
        <span>
            <Poptip word-wrap width="200" trigger="hover" :title="'Status: '+(jobcard.statusSummary || {}).name" :content="(jobcard.statusSummary || {}).description">
                <Tag color="success">{{ (jobcard.statusSummary || {}).name }}</Tag>
            </Poptip>
        </span>
    </span>

</template>
<script>
    export default {
        props: {
            /*  Jobcard data  */
            jobcard: Object,
            showText:{
                type: Boolean,
                default: true
            }
        },
    };
</script>