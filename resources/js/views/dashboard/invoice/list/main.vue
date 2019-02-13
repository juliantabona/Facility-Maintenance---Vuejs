<template>
    <Row :gutter="20">

        <Col :span="24">

            <!-- Get the page toolbar with back button and page title -->
            <pageToolbar :showBackBtn="false">
                
                <!-- Slot Main Title & Icon -->
                <template slot="title">
                    <Icon :style="{ marginTop:'-10px' }" type="ios-cash-outline" :size="30" class="mr-1"></Icon>
                    <h1 :style="{ fontSize:'2rem' }" class="text-dark d-inline">Invoices</h1>
                </template>

                <!-- Slot Extra functionality -->
                <template slot="extra">
                    <!-- Get the resource type button to allow user to toggle between getting company/branch specific data -->
                    <resourceTypeButton></resourceTypeButton>
                </template>


            </pageToolbar>

            <Row :gutter="20">

                <Col :span="24" :style="{ padding: '0 20px' }">

                    <!-- Get the invoice activity cards -->
                    <activityCardWidget url="/invoices/stats"></activityCardWidget>

                </Col>
                
            </Row>

            <!-- Get the filterable invoice list -->
            <invoiceListWidget></invoiceListWidget>

        </Col>
    </Row>
</template>
<script type="text/javascript">

    import pageToolbar from './../../../../components/_common/toolbars/pageToolbar.vue';
    import resourceTypeButton from './../../../../components/_common/buttons/resourceTypeButton.vue';
    import activityCardWidget from './../../../../widgets/activity/activityCardWidget.vue';
    import invoiceListWidget from './../../../../widgets/invoice/list/invoiceListWidget.vue';

    export default {
        components: { pageToolbar, resourceTypeButton, activityCardWidget, invoiceListWidget }
    }
</script>
