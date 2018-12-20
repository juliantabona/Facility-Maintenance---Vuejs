<template>
    
    <!-- Jobcard Start Date -->
    <span>
        <strong>Start Date: </strong>
        <Poptip word-wrap width="200" trigger="hover" :content="jobcard.start_date | moment('DD MMM YYYY, H:mmA')">
            <span v-if="jobcard.start_date">{{ jobcard.start_date | moment("DD MMM YYYY") }}</span>
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