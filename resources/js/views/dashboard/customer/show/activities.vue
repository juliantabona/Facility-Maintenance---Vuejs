<template>

    <Row :gutter="20">
        
        <Col span="20" offset="2">

            <!-- Get the page toolbar with back button and page title -->
            <pageToolbar :showBackBtn="true" :fallbackRoute="{ name: 'show-invoice', params: { id: invoiceId } }">
                
                <!-- Slot Main Title & Icon -->
                <template slot="title">
                    <Icon :style="{ marginTop:'-10px' }" type="ios-pulse-outline" :size="30" class="mr-1"></Icon>
                    <h1 :style="{ fontSize:'2rem' }" class="text-dark d-inline">{{ pageTitle }}</h1>
                </template>

            </pageToolbar>
            
        </Col>


        <Col v-if="activity_type" span="24">

            <Row :gutter="20">

                <!-- Get the filterable invoice activity list -->
                <activityListWidget :modelId="invoiceId" modelType="invoice" :activityType="activity_type"></activityListWidget>

            </Row>
            
        </Col>

        <Col v-else span="24">

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
                    <activityListWidget :modelId="invoiceId" modelType="invoice"></activityListWidget>

                </Col>

            </Row>
            
        </Col>


    </Row>
    
</template>
<script type="text/javascript">

    import pageToolbar from './../../../../components/_common/toolbars/pageToolbar.vue';
    import activityListWidget from './../../../../widgets/activity/activityListWidget.vue';
    import activityChartWidget from './../../../../widgets/activity/activityChartWidget.vue';

    export default {
        components: { pageToolbar, activityListWidget, activityChartWidget },
        data(){
            return {
                invoiceId: this.$route.params.id,
                activity_type: this.$route.query.activity_type
            }
        },
        watch: {
            //  Watch for changes on the invoice id
            '$route.params.id': function (id) {
                
                // react to route changes by updating invoice id...
                this.invoiceId = id;

            },
            //  Watch for changes on the invoice activity type
            '$route.query.activity_type': function (activity_type) {
                
                // react to route changes by updating invoice activity type...
                this.activity_type = activity_type;

            }
        },
        computed: {
            pageTitle: function(){

                if(this.activity_type == ''){
                    return 'Invoice Activities';
                }else if(this.activity_type == 'approved'){
                    return 'Approved Activities';
                }else if(this.activity_type == 'sent'){
                    return 'Sent Activities';
                }else if(this.activity_type == 'sent_receipt'){
                    return 'Sent Receipts Activities';
                }else if(this.activity_type == 'paid'){
                    return 'Paid Activities';
                }else{
                    return 'Activities';
                }
                
            }
        }
    }
</script>
