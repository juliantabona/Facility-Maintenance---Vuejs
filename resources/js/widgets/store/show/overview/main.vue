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

    .question-mark >>> i{
        font-size: 24px;
        line-height: 28px;
        margin-left: 0px;
    }

</style>

<template>
    
    <div>

        <Row>

            <Col :span="24">
                    
                <!-- Datetime Filters -->
                <Card class="mb-3">

                    <Row :gutter="20">

                        <Col :span="8">
                            <Select v-model="selectedDateFilter" filterable placeholder="Filter by date">

                                <Option v-for="item in ['Today', 'Yesterday', 'This Week', 'Last Week', 'This Month', 'Last Month', 'This Year', 'Last Year', 'Custom Date']" :value="item" :key="item">{{ item }}</Option>

                            </Select>
                        </Col>
                        
                        <template v-if="selectedDateFilter == 'Custom Date'">

                            <Col :span="6">
                                <DatePicker v-model="custom_start_date" type="date" placeholder="From"></DatePicker>
                            </Col>

                            <Col :span="6">
                                <DatePicker v-model="custom_end_date" type="date" placeholder="To"></DatePicker>
                            </Col>

                        </template>

                        <Col :span="(selectedDateFilter == 'Custom Date') ? 4 : 16">
                        
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

                        <Card v-if="mobileStoreStats">

                            <Row :gutter="20">
                                
                                <!-- Title -->
                                <Col span="24" class="border-bottom-dashed clearfix mb-3 pb-2">

                                    <span class="d-block float-left font-weight-bold text-dark">
                                        Mobile Store Conversion Rate
                                    </span>

                                    <Poptip 
                                        title="Mobile Store Conversion Rate"
                                        trigger="hover" placement="top-end" word-wrap width="300" class="d-block float-right"
                                        content="Shows sales and refunds of orders that are open/archieved and have been fully/partially paid">
                                        <Avatar icon="ios-help" size="small" class="question-mark" />
                                    </Poptip>

                                </Col>

                                <Col span="24">

                                    <mobileStoreStats :stats="mobileStoreStats"></mobileStoreStats>

                                </Col>

                            </Row>

                        </Card>

                    </Col>

                    <Col span="16">

                        <Row :gutter="20">

                            <!-- Sales & Refunds -->
                            <Col span="12" class="mb-2">

                                <Card v-if="salesAndRefundsChartData">
                                        
                                    <Row :gutter="20">
                                        
                                        <!-- Title -->
                                        <Col span="24" class="border-bottom-dashed clearfix mb-3 pb-2">

                                            <span class="d-block float-left font-weight-bold text-dark">
                                                Sales & Refunds
                                            </span>

                                            <Poptip 
                                                title="Sales & Refunds"
                                                trigger="hover" placement="top-end" word-wrap width="300" class="d-block float-right"
                                                content="Shows sales and refunds of orders that are open/archieved and have been fully/partially paid">
                                                <Avatar icon="ios-help" size="small" class="question-mark" />
                                            </Poptip>

                                        </Col>
                                        
                                        <!-- Details -->
                                        <Col span="24" class="mb-2">

                                            <Row :gutter="20" class="mb-2">
                                                
                                                <Col span="12">

                                                    <span class="d-block text-dark">
                                                        Sales ({{ totalSalesPercentage }}%)
                                                    </span>

                                                </Col>
                                                
                                                <Col span="12">

                                                    <span class="d-block text-dark text-right">
                                                        {{ currency + formatPrice(totalSalesAmount) }}
                                                    </span>

                                                </Col>

                                            </Row>

                                            <Row :gutter="20" class="mb-2">
                                                
                                                <Col span="12">

                                                    <span class="d-block text-danger">
                                                        Refunds ({{ totalRefundPercentage }}%)
                                                    </span>

                                                </Col>
                                                
                                                <Col span="12">

                                                    <span class="d-block text-danger text-right">
                                                        {{ currency + formatPrice(totalRefundAmount) }}
                                                    </span>

                                                </Col>

                                            </Row>
                                            
                                        </Col>
                                        
                                        <Col span="24" class="mb-2">

                                            <mainChart chartId="transactions-chart" :chartData="salesAndRefundsChartData"></mainChart>

                                        </Col>

                                    </Row>

                                </Card>

                            </Col>

                            <!-- Returning Customer Rate -->
                            <Col span="12" class="mb-2">
                                        
                                <Card v-if="returningCustomerRateChartData">

                                    <Row :gutter="20">
                                        
                                        <!-- Title -->
                                        <Col span="24" class="border-bottom-dashed mb-3 pb-2">

                                            <span class="d-block float-left font-weight-bold text-dark">
                                                Returning Customer Rate
                                            </span>

                                            <Poptip 
                                                title="Returning Customer Rate"
                                                trigger="hover" placement="top-end" word-wrap width="300" class="d-block float-right"
                                                content="Shows sales and refunds of orders that are open/archieved and have been fully/partially paid">
                                                <Avatar icon="ios-help" size="small" class="question-mark" />
                                            </Poptip>

                                        </Col>
                                        
                                        <!-- Details -->
                                        <Col span="24" class="mb-2">

                                            <Row :gutter="20" class="mb-2">
                                                
                                                <Col span="14">

                                                    <span class="d-block text-dark">
                                                        New Customers (75%)
                                                    </span>

                                                </Col>
                                                
                                                <Col span="10">

                                                    <span class="d-block text-dark text-right">
                                                        4500
                                                    </span>

                                                </Col>

                                            </Row>

                                            <Row :gutter="20" class="mb-2">
                                                
                                                <Col span="14">

                                                    <span class="d-block text-dark">
                                                        Return Customers (25%)
                                                    </span>

                                                </Col>
                                                
                                                <Col span="10">

                                                    <span class="d-block text-dark text-right">
                                                        1500
                                                    </span>

                                                </Col>

                                            </Row>
                                            
                                        </Col>
                                        
                                        <Col span="24" class="mb-2">
                                                    
                                            <mainChart chartId="returning-customer-rate-chart" :chartData="returningCustomerRateChartData"></mainChart>

                                        </Col>

                                    </Row>
                                    
                                </Card>

                            </Col>

                        </Row>

                        <Row :gutter="20">

                            <!-- Total Orders -->
                            <Col span="12" class="mb-2">
                                        
                                <Card v-if="totalOrdersChartData">

                                    <Row :gutter="20">
                                        
                                        <!-- Title -->
                                        <Col span="24" class="border-bottom-dashed clearfix mb-3 pb-2">

                                            <span class="d-block float-left font-weight-bold text-dark">
                                                Total Orders
                                            </span>

                                            <Poptip 
                                                title="Total Orders"
                                                trigger="hover" placement="top-end" word-wrap width="300" class="d-block float-right"
                                                content="Shows sales and refunds of orders that are open/archieved and have been fully/partially paid">
                                                <Avatar icon="ios-help" size="small" class="question-mark" />
                                            </Poptip>

                                        </Col>
                                        
                                        <Col span="24" class="mb-2">
                                                    
                                            <mainChart chartId="total-orders-chart" :chartData="totalOrdersChartData"></mainChart>

                                        </Col>

                                    </Row>
                                    
                                </Card>

                            </Col>

                            <!-- Average Order Value -->
                            <Col span="12" class="mb-2">
                                        
                                <Card v-if="averageOrderValueChartData">

                                    <Row :gutter="20">
                                        
                                        <!-- Title -->
                                        <Col span="24" class="border-bottom-dashed clearix mb-3 pb-2">

                                            <span class="d-block float-left font-weight-bold text-dark">
                                                Average Order Value
                                            </span>

                                            <Poptip 
                                                title="Average Order Value"
                                                trigger="hover" placement="top-end" word-wrap width="300" class="d-block float-right"
                                                content="Shows sales and refunds of orders that are open/archieved and have been fully/partially paid">
                                                <Avatar icon="ios-help" size="small" class="question-mark" />
                                            </Poptip>

                                        </Col>
                                        
                                        <Col span="24" class="mb-2">
                                                    
                                            <mainChart chartId="average-order-value-chart" :chartData="averageOrderValueChartData"></mainChart>

                                        </Col>

                                    </Row>
                                    
                                </Card>

                            </Col>

                        </Row>

                        <Row :gutter="20">

                            <!-- Popular Payment Methods -->
                            <Col span="12" class="mb-2">
                                        
                                <Card v-if="popularPaymentMethodsChartData">

                                    <Row :gutter="20">
                                        
                                        <!-- Title -->
                                        <Col span="24" class="border-bottom-dashed clearfix mb-3 pb-2">

                                            <span class="d-block float-left font-weight-bold text-dark">
                                                Popular Payment Methods
                                            </span>

                                            <Poptip 
                                                title="Popular Payment Methods"
                                                trigger="hover" placement="top-end" word-wrap width="300" class="d-block float-right"
                                                content="Shows sales and refunds of orders that are open/archieved and have been fully/partially paid">
                                                <Avatar icon="ios-help" size="small" class="question-mark" />
                                            </Poptip>

                                        </Col>
                                        
                                        <Col span="24" class="mb-2">
                                                    
                                            <mainChart chartId="popular-payment-methods-chart" :chartData="popularPaymentMethodsChartData"></mainChart>

                                        </Col>

                                    </Row>
                                    
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
                selectedDateFilter: [],
                custom_start_date: null,
                custom_end_date: null,
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
                    chartData['data']['datasets'][0]['data'] = this.stats.transactions.sale_transactions.data_intervals.map(function(interval){
                        return { 
                            //  Return the date on the x-axis
                            x: interval.date, 

                            //  Return the amount on the y-axis
                            y: interval.amount 
                        }
                    });

                    //  Update the data
                    chartData['data']['datasets'][1]['data'] = this.stats.transactions.refund_transactions.data_intervals.map(function(interval){
                        return { 
                            //  Return the date on the x-axis
                            x: interval.date, 

                            //  Return the amount on the y-axis (Multiply by negative 1 so that we move the refunds to the negative y-axis)
                            y: interval.amount * (-1)
                        }
                    });

                    //  Return the chart structure and data
                    return chartData;

                }

            },
            totalSalesAmount(){
                return this.stats.transactions.sale_transactions.total_amount;
            },
            totalRefundAmount(){
                return this.stats.transactions.refund_transactions.total_amount;
            },
            totalSalesPercentage(){
                var total = ( this.totalSalesAmount + this.totalRefundAmount );

                return total ? Math.round(this.totalSalesAmount / total * 100) : 0;
            },
            totalRefundPercentage(){
                var total = ( this.totalSalesAmount + this.totalRefundAmount );

                return total ? Math.round(this.totalRefundAmount / total * 100) : 0;
            },
            returningCustomerRateChartData(){

                if( this.stats ){

                    //  Get the chart template structure
                    let chartData = returningCustomerRateChartTemplate;

                    //  Update the data
                    chartData['data']['datasets'][0]['data'] = this.stats.customers.returning_customer_rate.new_customer_data_intervals.data_intervals.map(function(interval){
                        return { 
                            //  Return the date on the x-axis
                            x: interval.date, 

                            //  Return the count on the y-axis
                            y: interval.count 
                        }
                    });

                    //  Update the data
                    chartData['data']['datasets'][1]['data'] = this.stats.customers.returning_customer_rate.return_customer_data_intervals.data_intervals.map(function(interval){
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

                            //  Return the total amount on the y-axis
                            y: interval.total_amount 
                        }
                    });

                    //  Update the data
                    chartData['data']['datasets'][1]['data'] = this.stats.orders.orders_over_time.data_intervals.map(function(interval){
                        return { 
                            //  Return the date on the x-axis
                            x: interval.date, 

                            //  Return the average amount on the y-axis
                            y: interval.average_amount 
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

                    //  Update the data
                    chartData['data']['labels'] = this.stats.transactions.popular_payment_methods.data.map(function(record){
                        //  Get the payment method type e.g Airtime, Mobile Money, e.t.c
                        return record.payment_method
                    });

                    //  Update the data
                    chartData['data']['datasets'][0]['data'] = this.stats.transactions.popular_payment_methods.data.map(function(record){
                        //  Get the payment method count (Total) e.g 10, 20, e.t.c
                        return record.count
                    });

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

                    var postData = {
                        start_date: this.custom_start_date,
                        end_date: this.custom_end_date
                    };
                    
                    //  Use the api call() function located in resources/js/api.js
                    return api.call('post', this.store._links['oq:statistics'].href, postData)
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