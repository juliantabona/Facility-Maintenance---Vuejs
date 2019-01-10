<template>
    <Row :gutter="20">
        <Col :span="24">

            <!-- Get the resource type button to allow user to toggle between getting company/branch specific data -->
            <resourceTypeButton></resourceTypeButton>

        </Col>
        <Col :span="24">

            <!-- Get the filterable jobcard list -->
            <companyListWidget relationType="client"></companyListWidget>

        </Col>
    </Row>
</template>
<script type="text/javascript">

    import resourceTypeButton from './../../../../components/resourceTypeButton.vue';
    import companyListWidget from './../../../../widgets/company/companyListWidget.vue';

    export default {
        components: { resourceTypeButton, companyListWidget }
    }
</script>

