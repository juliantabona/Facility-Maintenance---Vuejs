<template>
    <Row :gutter="20">

        <!-- Show when we have customers -->
        <Col v-if="customerTotal && !isLoading" :span="24">

            <!-- Get the page toolbar with back button and page title -->
            <pageToolbar :showBackBtn="false">
                
                <!-- Slot Main Title & Icon -->
                <template slot="title">
                    <Icon :style="{ marginTop:'-10px', fontSize:'1.5rem' }" type="ios-cash-outline"></Icon>
                    <h1 :style="{ fontSize:'1.5rem' }" class="text-dark d-inline">Customers</h1>
                </template>

                <!-- Slot Extra functionality -->
                <template slot="extra">
                    <!-- Get the resource type button to allow user to toggle between getting company/branch specific data -->
                    <allocationTypeButton></allocationTypeButton>
                </template>


            </pageToolbar>

            <Row :gutter="20">

                <Col :span="24" :style="{ padding: '0 20px' }">

                    <!-- Get the customer activity cards -->
                    <activityCardWidget 
                        url="/customers/stats" 
                        routeName="customers"
                        :isMoneyList="['Paid', 'Outstanding', 'Expired', 'Cancelled', 'Sent', 'Approved', 'Draft']">
                    </activityCardWidget>

                </Col>
                
            </Row>

            <!-- Get the filterable customer list -->
            <customerListWidget></customerListWidget>

        </Col>

        <!-- Show when we don't have customers -->
        <Col v-else :span="20" :offset="2">
            <div class="pb-3 border-bottom">
                <h2>Customers</h2>
            </div>

            <Row :gutter="20">
                <Col :span="8">
                    <div class="mt-5 mb-2 pt-5 pb-3 clearfix border-bottom">
                        <h1 class="mb-3">Add your customers</h1>
                        <p class="mb-3" style="font-size:14px;">Get started by creating your first customer. You can also import your existing customers</p>

                        <!-- Add Customer Button -->
                        <basicButton @click.native="$router.push({ name:'create-invoice', query: { clientId: company.id } })" 
                                     size="large" class="float-left mb-3 mr-1">
                                     <span>+ Add Customer</span>
                        </basicButton>

                        <!-- Import Customer Button -->
                        <basicButton @click.native="$router.push({ name:'create-invoice', query: { clientId: company.id } })" 
                                     size="large" type="success" class="float-left mb-3">
                                     <Icon type="ios-cloud-upload-outline" :size="20" class="mr-1" />
                                     <span>Import</span>
                        </basicButton>

                    </div>
                     <span>Need help? <a href="#" class="font-weight-bold">Learn more <Icon type="ios-share-alt-outline" :size="20" style="margin-top: -9px;"/></a></span>
                </Col>
                <Col :span="16">
                    <img style="width:100%;" class="mt-5" src="/images/backgrounds/contact-phone.png">
                </Col>
            </Row>
        </Col>

    </Row>
</template>
<script type="text/javascript">

    import pageToolbar from './../../../../components/_common/toolbars/pageToolbar.vue';
    import allocationTypeButton from './../../../../components/_common/buttons/allocationTypeButton.vue';
    import activityCardWidget from './../../../../widgets/activity/activityCardWidget.vue';
    import customerListWidget from './../../../../widgets/invoice/list/invoiceListWidget.vue';

    import basicButton from './../../../../components/_common/buttons/basicButton.vue';

    export default {
        components: { pageToolbar, allocationTypeButton, activityCardWidget, customerListWidget, basicButton },
        data(){
            return {
                customerTotal: 0,
                isLoading: false,
            }
        },
        methods: {
            fetchCustomer() {

                //  Hold constant reference to the vue instance
                const self = this;

                //  Start loader
                self.isLoading = true;

                //  Console log to acknowledge the start of api process
                console.log('Start getting total number of customers...');

                //  Additional data to eager load along with the customer found
                var connections = '';

                //  Use the api call() function located in resources/js/api.js
                api.call('get', '/api/customers/?count=1'+connections)
                    .then(({data}) => {
                        
                        //  Console log the data returned
                        console.log(data);

                        //  Stop loader
                        self.isLoading = false;

                        //  Store the customers data
                        self.customerTotal = parseInt(data['companies']) + parseInt(data['users']);

                    })         
                    .catch(response => { 

                        //  Stop loader
                        self.isLoading = false;

                        //  Console log Error Location
                        console.log('dashboard/customer/show/main.vue - Error getting total number of customers...');

                        //  Log the responce
                        console.log(response);    
                    });
            }
        },
        created(){
            //  Fetch the customer
            this.fetchCustomer();
        }
    }
</script>
