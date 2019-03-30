<template>

    <Row :gutter="20">

        <Col span="20" offset="2">
            <jobcardSummaryWidget :create="true"></jobcardSummaryWidget>
        </Col>

    </Row>

</template>
<script>

    /*  Widgets   */
    import jobcardSummaryWidget from './../../../../widgets/jobcard/show/main.vue';

    export default {
        components: { 
          jobcardSummaryWidget
        }
    };
</script>