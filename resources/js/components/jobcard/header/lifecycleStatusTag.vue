<template>

    <!-- Jobcard Status -->
    <span>
        <strong>Status: </strong>
        <span>
            <Poptip word-wrap width="200" trigger="hover" title="Current Status" :content="(jobcard.statusSummary || {}).description">
                <Tag color="success">{{ (jobcard.statusSummary || {}).name }}</Tag>
            </Poptip>
        </span>
    </span>

</template>
<script>
    export default {
        props: [
            /*  Jobcard data  */
            'jobcard'
        ],
    };
</script>