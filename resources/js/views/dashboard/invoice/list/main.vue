<template>
    <Row :gutter="20">

        <Col :span="24">

            <!-- Get the page toolbar with back button and page title -->
            <pageToolbar :showBackBtn="false">
                
                <!-- Slot Main Title & Icon -->
                <template slot="title">
                    <Icon :style="{ marginTop:'-10px', fontSize:'1.5rem' }" type="ios-cash-outline"></Icon>
                    <h1 :style="{ fontSize:'1.5rem' }" class="text-dark d-inline">Invoices</h1>
                </template>

                <!-- Slot Extra functionality -->
                <template slot="extra">
                    <!-- Get the resource type button to allow user to toggle between getting company/branch specific data -->
                    <allocationTypeButton></allocationTypeButton>
                </template>


            </pageToolbar>

            <Row :gutter="20">

                <Col :span="24" :style="{ padding: '0 20px' }">

                    <!-- Get the invoice activity cards -->
                    <activityCardWidget 
                        url="/invoices/stats" 
                        routeName="invoices"
                        :isMoneyList="['Paid', 'Outstanding', 'Expired', 'Cancelled', 'Sent', 'Approved', 'Draft']">
                    </activityCardWidget>

                </Col>
                
            </Row>

            <!-- Get the filterable invoice list -->
            <invoiceListWidget></invoiceListWidget>

        </Col>
    </Row>
</template>
<script type="text/javascript">

    import pageToolbar from './../../../../components/_common/toolbars/pageToolbar.vue';
    import allocationTypeButton from './../../../../components/_common/buttons/allocationTypeButton.vue';
    import activityCardWidget from './../../../../widgets/activity/activityCardWidget.vue';
    import invoiceListWidget from './../../../../widgets/invoice/list/invoiceListWidget.vue';

    export default {
        components: { pageToolbar, allocationTypeButton, activityCardWidget, invoiceListWidget }
    }
</script>
