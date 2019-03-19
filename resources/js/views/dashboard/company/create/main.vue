<template>

    <Row :gutter="20">

        <Col span="20" offset="2">
            <invoiceSummaryWidget :create="true"></invoiceSummaryWidget>
        </Col>

    </Row>

</template>
<script>

    import invoiceSummaryWidget from './../../../../widgets/invoice/show/main.vue';


    export default {
        components: { 
          invoiceSummaryWidget
        }
    };
</script>