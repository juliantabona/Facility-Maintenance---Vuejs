<template>

    <Row :gutter="20">

        <Col span="20" offset="2">
            <productSummaryWidget :create="true"></productSummaryWidget>
        </Col>

    </Row>

</template>
<script>

    import productSummaryWidget from './../../../../widgets/product/show/main.vue';

    export default {
        components: { 
          productSummaryWidget
        }
    };
</script>