<template>

    <!-- Jobcard Authourized Badge -->
    <span>
        <strong>Authourized: </strong>
        <span class="mr-2">
            <Poptip word-wrap width="200" trigger="hover" content="Jobcard is authourized for processing">
                <span><Icon type="md-checkmark-circle-outline" :size="20" color="#19be6b"/></span>
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