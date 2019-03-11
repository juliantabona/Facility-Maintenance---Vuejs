<template>

    <Row :gutter="20">

        <Col span="20" offset="2">
            <quotationSummaryWidget :create="true"></quotationSummaryWidget>
        </Col>

    </Row>

</template>
<script>

    import quotationSummaryWidget from './../../../../widgets/quotation/show/main.vue';


    export default {
        components: { 
          quotationSummaryWidget
        }
    };
</script>