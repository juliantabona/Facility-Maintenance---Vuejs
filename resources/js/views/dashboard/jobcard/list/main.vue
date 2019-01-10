<template>
    <Row :gutter="20">
        <Col :span="24">

            <!-- Get the resource type button to allow user to toggle between getting company/branch specific data -->
            <resourceTypeButton></resourceTypeButton>

        </Col>
        <Col :span="24">

            <!-- Get the filterable jobcard list -->
            <jobcardListWidget></jobcardListWidget>

        </Col>
    </Row>
</template>
<script type="text/javascript">

    import resourceTypeButton from './../../../../components/resourceTypeButton.vue';
    import jobcardListWidget from './../../../../widgets/jobcard/jobcardListWidget.vue';

    export default {
        components: { resourceTypeButton, jobcardListWidget }
    }
</script>
