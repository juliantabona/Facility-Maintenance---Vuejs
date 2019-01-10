<template>

    <!-- Company Authourized Badge -->
    <span v-if="company.authourizedBy">
        <strong>Authourized: </strong>
        <span class="mr-2">
            <Poptip word-wrap width="200" trigger="hover" content="Company is authourized">
                <span><Icon type="md-checkmark-circle-outline" :size="20" color="#19be6b"/></span>
            </Poptip>
        </span>
    </span>
    
    <span  v-else="!company.authourizedBy" class="mr-2">
        <Poptip word-wrap width="200" trigger="hover" content="Authourize this company?">
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
            /*  Company data  */
            'company'
        ],
    };
</script>