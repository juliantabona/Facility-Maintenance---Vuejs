<template>
    <Row :gutter="20">
        <Col span="18" offset="3" :style="{ marginTop: '20px' }">

            <!-- Get the create jobcard widget -->
            <jobcardCreateWidget></jobcardCreateWidget>

        </Col>
    </Row>
</template>
<script type="text/javascript">

    import jobcardCreateWidget from './../../../../widgets/jobcard/jobcardCreateWidget.vue';

    export default {
        components: { jobcardCreateWidget }
    }
</script>
