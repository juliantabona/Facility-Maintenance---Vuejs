<style scoped>

    .roundedShape{
        width: 60px;
        height: 60px;
        border: 1px solid #c5c5c5;
        padding: 3px;
        margin: -15px 10px 0 0;
        border-radius: 100%;
        display:inline-block;
    }

    .order-table >>> .order-status{
        color: #000000;
    }

    .order-table >>> .order-status-success{
        color:#24d806;
    }

    .order-table >>> .order-status-fail{
        color:#ff0000;
    }

</style>

<template>

    <Row :gutter="20">

        <!-- Show when we have store -->
        <Col v-if="isLoadingStore" :span="12" :offset="6"> 
            <!-- Loader -->
            <Loader :loading="true" type="text" class="mt-5 text-left" theme="white">Loading store...</Loader>
        </Col>

        <!-- Show when we have store -->
        <Col v-if="store && !isLoadingStore" :span="22" :offset="1">
        
            <div class="mb-3 pb-3">
                <pageToolbar :fallbackRoute="{ name: 'stores' }"
                             :onlyBackBtn="true" class="d-inline-block mr-2">
                </pageToolbar>
                <img v-if="store.logo" :src="(store.logo || {}).url" alt="Store Logo" class="roundedShape">
                <h2 class="d-inline-block">{{ store.name }}</h2>
            </div>

            <div class="mt-3">
                <Card class="p-2">
                    <!-- Store Tabs -->
                    <Tabs type="card" :animated="false" class="pb-5">

                        <!-- Orders Tab -->
                        <TabPane label="Orders" class="p-2">
                            <!-- Loader -->
                            <Loader v-if="isLoadingOrders" :loading="true" type="text" class="mt-5 text-left" theme="white">Loading orders...</Loader>
                            
                            <!-- No orders message -->
                            <Alert v-if="!isLoadingOrders && !storeOrders" type="info" :style="{ maxWidth: '250px' }" show-icon>No orders yet</Alert>
                            
                            <Card v-if="!isLoadingOrders && storeOrders" class="mt-4 mb-3">
                                <Row :gutter="20">
                                    <Col :span="6">
                                        <Select v-model="selectedOrderStatuses" filterable multiple placeholder="Search customer...">
                                            <Option v-for="customer in storeOrders" :value="customer.id" :key="customer.id">
                                                {{ (customer.billing_info || {}).first_name || (customer.billing_info || {}).name }} {{ (customer.billing_info || {}).last_name }}
                                            </Option>
                                        </Select>
                                    </Col>
                                    <Col :span="6">
                                        <Select v-model="selectedOrderStatuses" filterable multiple placeholder="Filter by status">

                                            <OptionGroup label="Payment status">
                                                <Option v-for="item in ['Pending Payment', 'Failed Payment', 'Paid']" :value="item" :key="item">{{ item }}</Option>
                                            </OptionGroup>

                                            <OptionGroup label="Refund status">
                                                <Option v-for="item in ['Pending Refund', 'Refunded']" :value="item" :key="item">{{ item }}</Option>
                                            </OptionGroup>

                                            <OptionGroup label="Delivery status">
                                                <Option v-for="item in ['Pending Delivery', 'Delivered']" :value="item" :key="item">{{ item }}</Option>
                                            </OptionGroup>

                                            <OptionGroup label="Final status">
                                                <Option v-for="item in ['Cancelled', 'Completed']" :value="item" :key="item">{{ item }}</Option>
                                            </OptionGroup>

                                        </Select>
                                    </Col>
                                    <Col :span="4">
                                        <DatePicker type="date" placeholder="From"></DatePicker>
                                    </Col>
                                    <Col :span="4">
                                        <DatePicker type="date" placeholder="To"></DatePicker>
                                    </Col>
                                    <Col :span="4">
                                        <!-- Add Store Button -->
                                        <div class="clearfix">
                                            <basicButton @click.native="$router.push({ name:'create-order' })" 
                                                        size="large" class="float-right">
                                                        <span>+ Add Order</span>
                                            </basicButton>
                                        </div>
                                    </Col>
                                </Row>
                            </Card>

                            <!-- Store Orders -->
                            <Table v-if="!isLoadingOrders && storeOrders" :columns="columns" :data="storeOrders" class="order-table"></Table>
                        </TabPane>

                        <!-- Products Tab -->
                        <TabPane label="Products">

                            <Card class="pt-3 pb-3">
                                <Alert type="info" :style="{ maxWidth: '250px' }" show-icon>No products</Alert>
                            </Card>

                        </TabPane>

                        <!-- Customers Tab -->
                        <TabPane label="Customers">
                            
                            <Card class="pt-3 pb-3">
                                <Alert type="info" :style="{ maxWidth: '250px' }" show-icon>No customers</Alert>
                            </Card>

                        </TabPane>

                        <!-- Transactions Tab -->
                        <TabPane label="Transactions">
                            
                            <Card class="pt-3 pb-3">
                                <Alert type="info" :style="{ maxWidth: '250px' }" show-icon>No transactions</Alert>
                            </Card>

                        </TabPane>

                        <!-- Settings Tab -->
                        <TabPane label="Settings">
                            
                            <Card class="pt-3 pb-3">
                                <span>Settings Here</span>
                            </Card>

                        </TabPane>
                        
                    </Tabs>
                </Card>
            </div>

        </Col>

        <!-- Show when we don't have store -->
        <Col v-if="!store && !isLoadingStore" :span="20" :offset="2">
            <Row :gutter="20">
                <Col :span="8">
                    <div class="mt-5 mb-2 pt-5 pb-3 clearfix border-bottom">
                        <h1 class="mb-3">Couldn't Find Store</h1>
                        <p class="mb-3" style="font-size:14px;">Create a new store to sell products, services, events, memberships and even collect donations.</p>

                        <!-- Add Store Button -->
                        <basicButton @click.native="$router.push({ name:'create-store' })" 
                                    size="large" class="float-left mb-3 mr-1">
                                    <span>+ Create Store</span>
                        </basicButton>

                    </div>
                    <span>Need help? <a href="#" class="font-weight-bold">Learn more <Icon type="ios-share-alt-outline" :size="20" style="margin-top: -9px;"/></a></span>
                </Col>
                <Col :span="16">
                    <img style="width:100%;" class="mt-4" src="/images/backgrounds/mobile-ecommerce.png">
                </Col>
            </Row>
        </Col>
    </Row>

</template>

<script>
    
    import pageToolbar from './../../../components/_common/toolbars/pageToolbar.vue';
    
    /*  Buttons  */
    import basicButton from './../../../components/_common/buttons/basicButton.vue';

    /*  Loaders  */
    import Loader from './../../../components/_common/loaders/Loader.vue';  

    import orderRowDropDown from './orderRowDropDown.vue';  

    import lodash from 'lodash';
    Event.prototype._ = lodash;

    export default {
        components: { 
            pageToolbar, basicButton, Loader, orderRowDropDown
        },
        data(){
            return {
                store: null,
                storeId: this.$route.params.id,
                isLoadingStore: false,

                //  Orders
                storeOrders: null,
                isLoadingOrders: false,

                selectedOrderStatuses:[],

                columns: [
                    {
                        type: 'expand',
                        width: 30,
                        render: (h, params) => {
                            return h(orderRowDropDown, {
                                props: {
                                    order: params.row
                                }
                            })
                        }
                    },
                    {
                        width: 40,
                        title: '',
                        align: 'center',
                        render: (h, params) => {
                            return h('Poptip', {
                                        style: {
                                            width: '100%',
                                            textAlign:'left'
                                        },
                                        props: {
                                            width: 280,
                                            wordWrap: true,
                                            trigger:'hover',
                                            placement: 'right',
                                            title: params.row.status_title,
                                            content: params.row.status_description,
                                        }
                                    }, [
                                        h('Icon', {
                                                props: {
                                                    type: this.getOrderStatusIcon(params.row.status),
                                                    size: 24
                                                },
                                                class: ['order-status', this.getOrderStatusColor(params.row.status)]
                                            })
                                    ]);
                        }
                    },
                    {
                        width: 80,
                        title: 'Order #',
                        render: (h, params) => {
                            return h('span', (params.row.number));
                        }
                    },
                    {
                        width: 150,
                        title: 'Customer',
                        render: (h, params) => {
                            return h('span', (
                                ( (params.row.billing_info || {}).first_name || (params.row.billing_info || {} ).name )
                                +' '+ (params.row.billing_info || {}).last_name
                            ));
                        }
                    },
                    {
                        width: 200,
                        title: 'Email',
                        render: (h, params) => {
                            return h('span', ((params.row.billing_info || {}).email));
                        }
                    },
                    {
                        width: 150,
                        title: 'Phone',
                        render: (h, params) => {
                            return h('span', ((params.row.billing_info || {}).phone));
                        }
                    },
                    {
                        width: 150,
                        title: 'Date',
                        key: 'created_at_format'
                    },
                    {
                        title: 'Total',
                        key: 'grand_total'
                    },
                    {
                        title: 'Action',
                        align: 'center',
                        render: (h, params) => {
                            return h('div', [
                                h('Button', {
                                    props: {
                                        type: 'primary',
                                        size: 'small'
                                    },
                                    style: {
                                        marginRight: '5px'
                                    },
                                    on: {
                                        click: () => {
                                            this.$router.push({ name: 'show-order', params: { id: params.row.id } });
                                        }
                                    }
                                }, 'View')
                            ]);
                        }
                    }
                ],
 
            }
        },
        methods: {
            getOrderStatusIcon(status){

                if (status == 'completed') {
                    return 'ios-checkmark-circle-outline';
                }else if(status == 'paid') {
                    return 'ios-cash-outline';
                }else if(status == 'pending-payment') {
                    return 'ios-cash-outline';
                }else if(status == 'failed-payment') {
                    return 'ios-cash-outline';
                }else if(status == 'pending-delivery') {
                    return 'ios-basket-outline';
                }else if(status == 'delivered') {
                    return 'ios-basket-outline';
                }else if(status == 'pending-refund') {
                    return 'ios-repeat';
                }else if(status == 'refunded') {
                    return 'ios-repeat';
                }else if(status == 'cancelled') {
                    return 'ios-close-circle-outline';
                }

            },
            getOrderStatusColor(status){
                if (status == 'completed') {
                    return 'order-status-success';
                }else if(status == 'paid') {
                    return 'order-status-success';
                }else if(status == 'pending-payment') {
                    return '';
                }else if(status == 'failed-payment') {
                    return 'order-status-fail';
                }else if(status == '') {
                    return 'order-status-fail';
                }else if(status == 'delivered') {
                    return 'order-status-success';
                }else if(status == '') {
                    return 'order-status-fail';
                }else if(status == 'refunded') {
                    return 'order-status-success';
                }else if(status == 'cancelled') {
                    return 'order-status-fail';
                }
            },
            fetchStore() {

                if(this.storeId){

                    //  Hold constant reference to the vue instance
                    const self = this;

                    //  Start loader
                    self.isLoadingStore = true;

                    //  Console log to acknowledge the start of api process
                    console.log('Start getting store...');

                    //  Additional data to eager load along with the store found
                    var connections = '';

                    //  Use the api call() function located in resources/js/api.js
                    return api.call('get', '/api/stores/'+this.storeId+connections)
                        .then(({data}) => {
                            
                            //  Console log the data returned
                            console.log(data);

                            //  Stop loader
                            self.isLoadingStore = false;

                            //  Store the store data
                            self.store = data;

                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isLoadingStore = false;

                            //  Console log Error Location
                            console.log('dashboard/store/show/main.vue - Error getting store...');

                            //  Log the responce
                            console.log(response);    
                        });
                }

            },
            fetchOrders() {

                if( (this.store || {}).id ){

                    //  Hold constant reference to the vue instance
                    const self = this;

                    //  Start loader
                    self.isLoadingOrders = true;

                    //  Console log to acknowledge the start of api process
                    console.log('Start getting orders...');

                    //  Additional data to eager load along with the order found
                    var connections = '';

                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', '/api/orders?storeId='+this.store.id+connections)
                        .then(({data}) => {
                            
                            //  Console log the data returned
                            console.log(data);

                            //  Stop loader
                            self.isLoadingOrders = false;

                            //  Order the order data
                            self.storeOrders = data.data;

                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isLoadingOrders = false;

                            //  Console log Error Location
                            console.log('dashboard/order/show/main.vue - Error getting orders...');

                            //  Log the responce
                            console.log(response);    
                        });
                }

            }
        },
        created(){
            //  Fetch the store
            var self = this;
            this.fetchStore().then( data => {
                //  After getting the store then fetch the orders
                self.fetchOrders();
            });
        }
    };
  
</script>