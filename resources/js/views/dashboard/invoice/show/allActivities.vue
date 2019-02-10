<template>

    <Row :gutter="20">
        
        <Col span="20" offset="2">

            <!-- Get the page toolbar with back button and page title -->
            <pageToolbar :showBackBtn="true" :fallbackRoute="{ name: 'show-invoice', params: { id: invoiceId } }">
                
                <!-- Slot Main Title & Icon -->
                <template slot="title">
                    <Icon :style="{ marginTop:'-10px' }" type="ios-pulse-outline" :size="30" class="mr-1"></Icon>
                    <h1 :style="{ fontSize:'2rem' }" class="text-dark d-inline">Invoice Activities</h1>
                </template>

            </pageToolbar>
            
        </Col>

        <Col span="24">

            <Row :gutter="20">
                
                <Col span="8">

                    <!-- Get the invoice activity chart -->
                    <activityChartWidget 
                        :modelId="invoiceId" modelType="invoice" allocation="company" count="1" groupBy="type"
                        chartLabel="Activity Summary" chartType="bar" :chartOptions="null" :chartHeight="250">
                    </activityChartWidget>

                </Col>

                <Col span="24">

                    <!-- Get the filterable invoice activity list -->
                    <invoiceActivityListWidget :invoiceId="invoiceId"></invoiceActivityListWidget>

                </Col>

            </Row>
            
        </Col>


    </Row>
    
</template>
<script type="text/javascript">

    import pageToolbar from './../../../../components/_common/toolbars/pageToolbar.vue';
    import invoiceActivityListWidget from './../../../../widgets/invoice/list/invoiceActivityListWidget.vue';
    import activityChartWidget from './../../../../widgets/activity/activityChartWidget.vue';

    export default {
        components: { pageToolbar, invoiceActivityListWidget, activityChartWidget },
        data(){
            return {
                invoiceId: this.$route.params.id
            }
        },
        watch: {
            //  Watch for changes on the invoice id
            '$route.params.id': function (id) {
                
                // react to route changes by updating invoice id...
                this.invoiceId = id;

            }
        }
    }
</script>
