<template>
    
    <!-- Jobcard Created Date -->
    <span>
        <strong>Created Date: </strong>
        <Poptip word-wrap width="200" trigger="hover" :content="jobcard.created_at | moment('DD MMM YYYY, H:mmA')">
            <span v-if="jobcard.created_at">{{ jobcard.created_at | moment("DD MMM YYYY") }}</span>
        </Poptip>
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