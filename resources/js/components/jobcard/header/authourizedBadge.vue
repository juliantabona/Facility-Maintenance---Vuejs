<template>

    <!-- Jobcard Authourized Badge -->
    <span v-if="jobcard.authourizedBy">
        <strong>Authourized: </strong>
        <span class="mr-2">
            <Poptip word-wrap width="200" trigger="hover" content="Jobcard is authourized for processing">
                <span><Icon type="md-checkmark-circle-outline" :size="20" color="#19be6b"/></span>
            </Poptip>
        </span>
    </span>
    
    <span  v-else="!jobcard.authourizedBy" class="mr-2">
        <Poptip word-wrap width="200" trigger="hover" content="Authourize this jobcard?">
            <Button type="primary" size="small">
                <Icon type="md-lock" :size="16" :style="{ marginTop:'-3px' }"/>
                Authourize
            </Button>
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