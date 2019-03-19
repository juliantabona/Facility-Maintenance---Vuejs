<template>
    <Row :gutter="20">

        <Col :span="24">

            <!-- Get the page toolbar with back button and page title -->
            <pageToolbar :showBackBtn="false">
                
                <!-- Slot Main Title & Icon -->
                <template slot="title">
                    <Icon :style="{ marginTop:'-10px', fontSize:'1.5rem' }" type="ios-cash-outline"></Icon>
                    <h1 :style="{ fontSize:'1.5rem' }" class="text-dark d-inline">Quotations</h1>
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
                    <activityCardWidget 
                        url="/quotations/stats" 
                        routeName="quotations"
                        :isMoneyList="['Converted', 'Unconverted', 'Expired', 'Sent', 'Approved', 'Draft']">
                    </activityCardWidget>

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
