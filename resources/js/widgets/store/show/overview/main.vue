<style scoped>

    .summary-card {
        border-top: 5px solid transparent;
        padding: 10px 5px;
        border-radius: 0;
    }

    .summary-card.active {
        border-top: 5px solid #2d8cf0;
    }

    .summary-card >>> .main-heading {
        display: block;
        font-size: 11px;
        margin-bottom: 16px;
        text-transform: uppercase;
        color: #6c7781;
    }

    .summary-card >>> .main-figure {
        font-size: 18px;
        font-weight: 500;
        color: #191e23;
        margin-bottom: 4px;
    }

    .summary-card >>> .sub-heading, 
    .summary-card >>> .sub-figure {
        font-size: 13px;
        color: #555d66;
        display: block;
    }

    .column-equal-height {
        display: flex;
    }

    .full-height{
        height: 100%;
    }

</style>

<template>
    
    <div>

        <Row>

            <Col :span="24">

                <el-tabs value="first">
                    
                    <!-- Search / Filter Tools -->
                    <el-tab-pane label="Search / Filter" name="first">
                
                        <Card class="mb-3">
                            <Row :gutter="20">
                                <Col :span="8">
                                    <Select v-model="selectedOrderStatuses" filterable multiple placeholder="Filter by status">

                                        <OptionGroup label="Payment status">
                                            <Option v-for="item in ['Pending Payment', 'Verify Payment', 'Failed Payment', 'Paid']" :value="item" :key="item">{{ item }}</Option>
                                        </OptionGroup>

                                        <OptionGroup label="Refund status">
                                            <Option v-for="item in ['Pending Refund', 'Refunded']" :value="item" :key="item">{{ item }}</Option>
                                        </OptionGroup>

                                        <OptionGroup label="Delivery status">
                                            <Option v-for="item in ['Pending Delivery', 'Verify Delivery','Delivered']" :value="item" :key="item">{{ item }}</Option>
                                        </OptionGroup>

                                        <OptionGroup label="Final status">
                                            <Option v-for="item in ['Cancelled', 'Completed']" :value="item" :key="item">{{ item }}</Option>
                                        </OptionGroup>

                                    </Select>
                                </Col>
                                <Col :span="6">
                                    <DatePicker type="date" placeholder="From"></DatePicker>
                                </Col>
                                <Col :span="6">
                                    <DatePicker type="date" placeholder="To"></DatePicker>
                                </Col>
                                <Col :span="4">
                                    <!-- Refresh Orders Button -->
                                    <div class="clearfix">
                                        <basicButton @click.native="fetchStoreStats()" 
                                                    size="default" class="float-right mr-4"
                                                    :disabled="isLoadingStats">
                                                    <Icon type="ios-refresh" :size="20"/>
                                                    <span>Refresh</span>
                                        </basicButton>
                                    </div>
                                </Col>
                            </Row>
                        </Card>

                    </el-tab-pane>
                    
                    <!-- Manage Table Columns Tools -->
                    <el-tab-pane label="Manage Columns" name="second">

                        <Card class="mb-3">
                            <Row :gutter="12">

                                <Col :span="24" class="clearfix">
                                    <span class="font-weight-bold d-block mt-2 mb-3">Select stats to show:</span>
                                </Col>

                                <Col :span="24" class="clearfix">
                                            
                                    <!-- Table Stat Checkboxes -->
                                    <CheckboxGroup v-model="tableColumnsToShowByDefault" class="mb-3">
                                        <Checkbox v-for="(stat, i) in orderStats" :key="i" :label="stat.name"></Checkbox>
                                    </CheckboxGroup>

                                </Col>

                            </Row>
                        </Card>

                    </el-tab-pane>

                </el-tabs>

            </Col>
            
            <Col span="24">

                <Row>
                    
                    <!-- Saving Spinner  -->
                    <Spin v-if="isLoadingStats" size="large" fix></Spin>

                    <!-- Statastics -->
                    <Col span="8" v-for="(stat, i) in orderStats" :key="i">

                        <Card :class="'summary-card' + (activeCard == i ? ' active' : '')" 
                            @click.native="activeCard = i">

                            <!-- Main Title -->
                            <span class="main-heading">{{ stat.name }}</span>

                            <Row>
                                <Col span="20">

                                    <!-- Current Period Amount -->
                                    <span class="main-figure">{{ formatPrice(stat.amount, currency) }}</span>
                                </Col>
                                <Col span="4">
                                    <Icon type="md-arrow-forward" />

                                    <!-- Percentage -->
                                    <span class="percentage">0%</span>

                                </Col>
                            </Row>

                            <!-- Previous Period Amount -->
                            <span class="sub-heading">Previous Year</span>
                            <span class="sub-figure">P0.00</span>
                        </Card>
                    </Col>
                    
                </Row>

                <Row :gutter="20" class="mt-4">

                    <!-- Mobile Store Statastics -->
                    <Col span="8">

                        <Card v-if="mobileStoreStats" class="summary-card">

                            <mobileStoreStats :stats="mobileStoreStats"></mobileStoreStats>

                        </Card>

                    </Col>

                    <Col span="16">

                        <Row :gutter="20">

                            <!-- Sales & Refunds -->
                            <Col span="12" class="mb-2">

                                <Card v-if="salesAndRefundsChartData">
                                        
                                    <mainChart chartId="transactions-chart" :chartData="salesAndRefundsChartData"></mainChart>

                                </Card>

                            </Col>

                            <!-- Returning Customer Rate -->
                            <Col span="12" class="mb-2">

                                <Card v-if="returningCustomerRateChartData">
                                        
                                    <mainChart chartId="returning-customer-rate-chart" :chartData="returningCustomerRateChartData"></mainChart>

                                </Card>

                            </Col>

                            <!-- Total Orders -->
                            <Col span="12" class="mb-2">

                                <Card v-if="totalOrdersChartData">
                                        
                                    <mainChart chartId="total-orders-chart" :chartData="totalOrdersChartData"></mainChart>

                                </Card>

                            </Col>

                            <!-- Average Order Value -->
                            <Col span="12" class="mb-2">

                                <Card v-if="averageOrderValueChartData">
                                    
                                    <mainChart chartId="average-order-value-chart" :chartData="averageOrderValueChartData"></mainChart>

                                </Card>

                            </Col>

                            <!-- Popular Payment Methods -->
                            <Col span="12" class="mb-2">

                                <Card v-if="popularPaymentMethodsChartData">
                                    
                                    <mainChart chartId="popular-payment-methods-chart" :chartData="popularPaymentMethodsChartData"></mainChart>

                                </Card>

                            </Col>

                            

                        </Row>

                    </Col>

                </Row>

            </Col>
            
        </Row>

    </div>

</template>

<script>

    /*  Buttons  */
    import basicButton from './../../../../components/_common/buttons/basicButton.vue';

    /*  Loaders  */
    import Loader from './../../../../components/_common/loaders/Loader.vue';  

    /*  Local Widgets  */
    import mobileStoreStats from './mobileStoreStats/main.vue';

    //  Import the total orders template
    import salesAndRefundsChartTemplate from './charts/salesAndRefundsChartTemplate.js';

    //  Import the total orders template
    import returningCustomerRateChartTemplate from './charts/returningCustomerRateChartTemplate.js';

    //  Import the total orders template
    import totalOrdersChartTemplate from './charts/totalOrdersChartTemplate.js';

    //  Import the total orders template
    import averageOrderValueChartTemplate from './charts/averageOrderValueChartTemplate.js';

    //  Import the total orders template
    import popularPaymentMethodsChartTemplate from './charts/popularPaymentMethodsChartTemplate.js';

    /*  Local Widgets  */
    import mainChart from './charts/chart.vue';

    export default {
        props:{
            store: {
                type: Object,
                default: null
            }
        },
        components: { 
            basicButton, Loader, mobileStoreStats,  mainChart
        },
        data(){
            return {
                currency: (this.store.currency || {}).symbol || (this.store.currency || {}).code,
                tableColumnsToShowByDefault: [],
                selectedOrderStatuses: [],
                isLoadingStats: false,
                activeCard: null,
                stats: null
            }
        },
        computed: {
            salesAndRefundsChartData(){

                if( this.stats ){

                    //  Get the chart template structure
                    let chartData = salesAndRefundsChartTemplate;

                    //  Update the data
                    chartData['data']['datasets'][0]['data'] = this.stats.orders.orders_over_time.data_intervals.map(function(interval){
                        return { 
                            //  Return the date on the x-axis
                            x: interval.date, 

                            //  Return the count on the y-axis
                            y: interval.count 
                        }
                    });

                    //  Update the data
                    chartData['data']['datasets'][1]['data'] = this.stats.orders.orders_over_time.data_intervals.map(function(interval){
                        return { 
                            //  Return the date on the x-axis
                            x: interval.date, 

                            //  Return the count on the y-axis
                            y: interval.count * -1
                        }
                    });

                    //  Return the chart structure and data
                    return chartData;

                }

            },
            returningCustomerRateChartData(){

                if( this.stats ){

                    //  Get the chart template structure
                    let chartData = returningCustomerRateChartTemplate;

                    //  Update the data
                    chartData['data']['datasets'][0]['data'] = this.stats.orders.orders_over_time.data_intervals.map(function(interval){
                        return { 
                            //  Return the date on the x-axis
                            x: interval.date, 

                            //  Return the count on the y-axis
                            y: interval.count 
                        }
                    });

                    //  Return the chart structure and data
                    return chartData;

                }

            },
            totalOrdersChartData(){

                if( this.stats ){

                    //  Get the chart template structure
                    let chartData = totalOrdersChartTemplate;

                    //  Update the data
                    chartData['data']['datasets'][0]['data'] = this.stats.orders.orders_over_time.data_intervals.map(function(interval){
                        return { 
                            //  Return the date on the x-axis
                            x: interval.date, 

                            //  Return the count on the y-axis
                            y: interval.count 
                        }
                    });

                    //  Return the chart structure and data
                    return chartData;

                }

            },
            averageOrderValueChartData(){

                if( this.stats ){

                    //  Get the chart template structure
                    let chartData = averageOrderValueChartTemplate;

                    //  Update the data
                    chartData['data']['datasets'][0]['data'] = this.stats.orders.orders_over_time.data_intervals.map(function(interval){
                        return { 
                            //  Return the date on the x-axis
                            x: interval.date, 

                            //  Return the count on the y-axis
                            y: interval.count 
                        }
                    });

                    //  Return the chart structure and data
                    return chartData;

                }

            },
            popularPaymentMethodsChartData(){

                if( this.stats ){

                    //  Get the chart template structure
                    let chartData = popularPaymentMethodsChartTemplate;

                    /*
                    //  Update the data
                    chartData['data']['datasets'][0]['data'] = this.stats.orders.orders_over_time.data_intervals.map(function(interval){
                        return { 
                            //  Return the date on the x-axis
                            x: interval.date, 

                            //  Return the count on the y-axis
                            y: interval.count 
                        }
                    });
                    */

                    //  Return the chart structure and data
                    return chartData;

                }

            },

            



            orderStats(){

                return ((this.stats || {}).orders || []).financial || [
                    {
                        name: '...',
                        amount: 0
                    },
                    {
                        name: '...',
                        amount: 0
                    },
                    {
                        name: '...',
                        amount: 0
                    },
                    {
                        name: '...',
                        amount: 0
                    },
                    {
                        name: '...',
                        amount: 0
                    },
                    {
                        name: '...',
                        amount: 0
                    }
                ];
            },
            mobileStoreStats(){

                return (this.stats || {}).mobile_store;

            }
        },
        methods: {
            fetchStoreStats() {

                if( ((this.store || {})._links['oq:statistics'] || {}).href ){

                    //  Hold constant reference to the vue instance
                    const self = this;

                    //  Start loader
                    self.isLoadingStats = true;

                    //  Console log to acknowledge the start of api process
                    console.log('Start getting store statistics...');
                    
                    //  Use the api call() function located in resources/js/api.js
                    return api.call('get', this.store._links['oq:statistics'].href )
                        .then(({data}) => {
                            
                            //  Console log the data returned
                            console.log(data);

                            //  Stop loader
                            self.isLoadingStats = false;

                            //  Store the store data
                            self.stats = data;

                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isLoadingStats = false;

                            //  Console log Error Location
                            console.log('dashboard/store/show/main.vue - Error getting store statistics...');

                            //  Log the responce
                            console.log(response);    
                        });
                }

            },
            formatPrice(money, symbol) {
                let val = (money/1).toFixed(2).replace(',', '.');
                return (symbol ? symbol : '') + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }
        },
        created(){

            this.fetchStoreStats();

        }
    };
  
</script>