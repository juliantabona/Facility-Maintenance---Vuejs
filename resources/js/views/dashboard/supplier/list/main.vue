<template>
    <Row :gutter="20">
        <Col :span="24">
            <supplierList modelType="company" type="supplier"></supplierList>
        </Col>
    </Row>
</template>
<script type="text/javascript">
    import supplierList from './../../../../components/company/company-list.vue';
    export default {
        components: { supplierList }
    }
</script>