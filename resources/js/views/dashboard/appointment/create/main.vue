<template>

    <Row :gutter="20">

        <Col span="20" offset="2">
            <appointmentSummaryWidget :create="true"></appointmentSummaryWidget>
        </Col>

    </Row>

</template>
<script>

    /*  Widgets   */
    import appointmentSummaryWidget from './../../../../widgets/appointment/show/main.vue';

    export default {
        components: { 
          appointmentSummaryWidget
        }
    };
</script>