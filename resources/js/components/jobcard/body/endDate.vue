<template>
    
    <!-- Jobcard End Date -->
    <span>
        <strong>End Date: </strong>
        <Poptip word-wrap width="200" trigger="hover" :content="jobcard.end_date | moment('DD MMM YYYY, H:mmA')">
            <span v-if="jobcard.end_date">{{ jobcard.end_date | moment("DD MMM YYYY") }}</span>
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