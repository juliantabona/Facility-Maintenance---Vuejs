<template>

    <Row :gutter="20">
        
        <Col span="20" offset="2">

            <!-- Get the page toolbar with back button and page title -->
            <pageToolbar :showBackBtn="true" :fallbackRoute="{ name: 'show-company', params: { id: companyId } }">
                
                <!-- Slot Main Title & Icon -->
                <template slot="title">
                    <Icon :style="{ marginTop:'-10px' }" type="ios-pulse-outline" :size="30" class="mr-1"></Icon>
                    <h1 :style="{ fontSize:'2rem' }" class="text-dark d-inline">{{ pageTitle }}</h1>
                </template>

            </pageToolbar>
            
        </Col>


        <Col v-if="activity_type" span="24">

            <Row :gutter="20">

                <!-- Get the filterable company activity list -->
                <activityListWidget :modelId="companyId" modelType="company" :activityType="activity_type"></activityListWidget>

            </Row>
            
        </Col>

        <Col v-else span="24">

            <Row :gutter="20">
                
                <Col span="8">

                    <!-- Get the company activity chart -->
                    <activityChartWidget 
                        :modelId="companyId" modelType="company" allocation="company" count="1" groupBy="type"
                        chartLabel="Activity Summary" chartType="bar" :chartOptions="null" :chartHeight="250">
                    </activityChartWidget>

                </Col>

                <Col span="24">

                    <!-- Get the filterable company activity list -->
                    <activityListWidget :modelId="companyId" modelType="company"></activityListWidget>

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
                companyId: this.$route.params.id,
                activity_type: this.$route.query.activity_type
            }
        },
        watch: {
            //  Watch for changes on the company id
            '$route.params.id': function (id) {
                
                // react to route changes by updating company id...
                this.companyId = id;

            },
            //  Watch for changes on the company activity type
            '$route.query.activity_type': function (activity_type) {
                
                // react to route changes by updating company activity type...
                this.activity_type = activity_type;

            }
        },
        computed: {
            pageTitle: function(){

                return 'Activities';
                
            }
        }
    }
</script>
