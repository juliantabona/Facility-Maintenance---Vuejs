<template>
    <Row :gutter="20">

        <Col :span="24">

            <!-- Get the page toolbar with back button and page title -->
            <pageToolbar :showBackBtn="false">
                
                <!-- Slot Main Title & Icon -->
                <template slot="title">
                    <Icon :style="{ marginTop:'-10px' }" type="ios-cash-outline" :size="30" class="mr-1"></Icon>
                    <h1 :style="{ fontSize:'2rem' }" class="text-dark d-inline">Quotations</h1>
                </template>

                <!-- Slot Extra functionality -->
                <template slot="extra">
                    <!-- Get the resource type button to allow user to toggle between getting company/branch specific data -->
                    <allocationTypeButton></allocationTypeButton>
                </template>


            </pageToolbar>

            <Row :gutter="20">

                <Col :span="24" :style="{ padding: '0 20px' }">

                    <!-- Get the quotation activity cards -->
                    <activityCardWidget url="/quotations/stats"></activityCardWidget>

                </Col>
                
            </Row>

            <!-- Get the filterable quotation list -->
            <quotationListWidget></quotationListWidget>

        </Col>
    </Row>
</template>
<script type="text/javascript">

    import pageToolbar from './../../../../components/_common/toolbars/pageToolbar.vue';
    import allocationTypeButton from './../../../../components/_common/buttons/allocationTypeButton.vue';
    import activityCardWidget from './../../../../widgets/activity/activityCardWidget.vue';
    import quotationListWidget from './../../../../widgets/quotation/list/quotationListWidget.vue';

    export default {
        components: { pageToolbar, allocationTypeButton, activityCardWidget, quotationListWidget }
    }
</script>
