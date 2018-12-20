<template>

    <Row>

        <Col span="24">

            <!-- Jobcard view button -->

            <jobcardViewButton v-if="showViewBtn" :route="{ name: 'show-jobcard', params: { id: jobcard.id } }"></jobcardViewButton>

            <!-- Jobcard download button -->

            <jobcardDownloadButton v-if="showDownloadBtn" :jobcardId="jobcard.id"></jobcardDownloadButton>

            <!-- Jobcard send button -->
            
             <jobcardSendButton v-if="showSendBtn"></jobcardSendButton>

            <!-- Jobcard make public switch -->

            <jobcardMakePublicSwitch v-if="showPublicBtn" v-bind="$props"></jobcardMakePublicSwitch>

        </Col>
    </Row>

</template>
<script>
    import jobcardViewButton from './../../_common/button/view.vue';
    import jobcardDownloadButton from './downloadButton.vue';
    import jobcardSendButton from './../../_common/button/send.vue';
    import jobcardMakePublicSwitch from './makePublic.vue';

    export default {
        props: [
            /*  Jobcard data  */
            'jobcard',
            /*  Jobcard footer related properties  */
            'showViewBtn', 'showDownloadBtn', 'showSendBtn', 'showPublicBtn'
            
        ],
        components: { 
            jobcardViewButton, jobcardDownloadButton, jobcardSendButton, jobcardMakePublicSwitch   
        }
    };
</script>