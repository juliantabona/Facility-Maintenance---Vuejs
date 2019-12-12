<style scoped>

    .order-card >>> .ivu-card-head{
        padding-bottom:0 !important;
    }

    .order-card >>> .order-menu {
        height: 35px;
        line-height: 30px;
        background: transparent;
    }

    .order-card >>> .order-menu:after {
        background: transparent;
    }

    .order-card >>> .order-menu li {
        padding: 0 15px;
        font-size: 12px !important;
    }

    .order-search-bar >>> input {
        height: 30px;
        border: none;
    }

    .order-dropdown-item >>> .ivu-select-dropdown {
        top: 30px !important;
        left: 0px !important;
    }

    .order-table >>> .breakdown-poptip .ivu-poptip-body{
        padding: 0 16px;
    }

    .order-table >>> .breakdown-poptip .ivu-list-container{
        margin-top: -20px !important;
        margin-bottom: -20px !important;
    }

    .order-table, .order-table >>> .ivu-table{
        overflow: inherit !important;
    }

</style>

<template>

    <div>

        <!-- Manage Columns Button / Add Order Button -->
        <Row class="border-bottom pb-3">

            <Col :span="4">

                <h2 class="font-weight-bold pl-4">Orders</h2>

            </Col>

            <Col :span="20">
                <div class="clearfix">
                    
                    <!-- Add Order Button -->
                    <basicButton @click.native="$router.push({ name:'create-order' })" 
                                size="large" class="float-right">
                                <span>Create Order</span>
                    </basicButton>

                </div>
            </Col>

        </Row>

        <el-tabs v-if="false" value="first">
            
            <!-- Search / Filter Tools -->
            <el-tab-pane label="Search / Filter" name="first">
        
                <Card class="mb-3">
                    <Row :gutter="20">
                        <Col :span="6">
                            <Select v-model="selectedOrderStatuses" filterable multiple placeholder="Search customer...">
                                <Option v-for="customer in localOrders" :value="customer.id" :key="customer.id">
                                    {{ (customer.billing_info || {}).first_name || (customer.billing_info || {}).name }} {{ (customer.billing_info || {}).last_name }}
                                </Option>
                            </Select>
                        </Col>
                        <Col :span="6">
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
                        <Col :span="4">
                            <DatePicker type="date" placeholder="From"></DatePicker>
                        </Col>
                        <Col :span="4">
                            <DatePicker type="date" placeholder="To"></DatePicker>
                        </Col>
                        <Col :span="4">
                            <!-- Refresh Orders Button -->
                            <div class="clearfix">
                                <basicButton @click.native="fetchOrders()" 
                                            size="default" class="float-right mr-4"
                                            :disabled="isLoadingOrders">
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
                            <span class="font-weight-bold pl-4 d-block mt-2 mb-3">Select Columns to show:</span>
                        </Col>

                        <Col :span="24" class="clearfix">
                                    
                            <!-- Table Column Checkboxes -->
                            <CheckboxGroup  
                                v-model="tableColumnsToShowByDefault" class="mb-3">
                                <Checkbox label="Order #"></Checkbox>
                                <Checkbox label="Customer"></Checkbox>
                                <Checkbox label="Email"></Checkbox>
                                <Checkbox label="Phone"></Checkbox>
                                <Checkbox label="Date"></Checkbox>
                                <Checkbox label="Grand Total"></Checkbox>
                            </CheckboxGroup>

                        </Col>

                    </Row>
                </Card>

            </el-tab-pane>

        </el-tabs>


        <Card dis-hover class="order-card p-2">

            <Row slot="title">
                <Col :span="24">

                    <Menu mode="horizontal" theme="light" :active-name="activeTab" class="order-menu"
                          @on-select="changeActiveTab($event)">
                        <MenuItem name="all">
                            <span class="font-weight-bold">All</span>
                        </MenuItem>
                        <MenuItem name="open">
                            <Icon type="ios-browsers-outline" :size="20" />
                            <span class="font-weight-bold">Open</span>
                        </MenuItem>
                        <MenuItem name="archieved">
                            <Icon type="ios-archive-outline" :size="20" />
                            <span class="font-weight-bold">Archieved</span>
                        </MenuItem>
                        <MenuItem name="cancelled">
                            <Icon type="ios-hand-outline" :size="20" />
                            <span class="font-weight-bold">Cancelled</span>
                        </MenuItem>
                    </Menu>

                </Col>
            </Row>
            
            <Row class="mb-4">

                <Col :span="18">

                    <ButtonGroup>

                        <Button class="p-0">
                            <Input v-model="searchedOrder" prefix="ios-search" placeholder="Search orders..." 
                                   class="order-search-bar" style="width: 300px;" />
                        </Button>

                        <!-- Payment Status Button -->
                        <Button>

                            <!-- Dropdown -->
                            <Dropdown trigger="custom" class="order-dropdown-item"
                                      :visible="paymentFilterIsVisible">
                                
                                <!-- Title -->
                                <span @click="paymentFilterIsVisible = true">
                                    Payment Status
                                    <Icon type="ios-arrow-down"></Icon>
                                </span>

                                <!-- Dropdown Options -->
                                <DropdownMenu slot="list">
                                    
                                    <div class="p-2 pl-3 pr-5 text-left">

                                        <!-- Status Checkboxes -->
                                        <CheckboxGroup v-model="selectedPaymentStatuses">
                                            <Checkbox class="d-block" label="Authorized"></Checkbox>
                                            <Checkbox class="d-block" label="Paid"></Checkbox>
                                            <Checkbox class="d-block" label="Partially refunded"></Checkbox>
                                            <Checkbox class="d-block" label="Partially paid"></Checkbox>
                                            <Checkbox class="d-block" label="Pending"></Checkbox>
                                            <Checkbox class="d-block" label="Refunded"></Checkbox>
                                            <Checkbox class="d-block" label="Unpaid"></Checkbox>
                                            <Checkbox class="d-block" label="Voided"></Checkbox>
                                        </CheckboxGroup>

                                        <div class="mt-4">

                                            <!-- Apply Filter Button -->
                                            <Button type="primary" @click="paymentFilterIsVisible = false">Apply Filter(s)</Button>

                                        </div>

                                    </div>

                                </DropdownMenu>

                            </Dropdown>

                        </Button>

                        <!-- Fulfillment Status Button -->
                        <Button>

                            <!-- Dropdown -->
                            <Dropdown trigger="custom" class="order-dropdown-item"
                                      :visible="fulfillmentFilterIsVisible">
                                
                                <!-- Title -->
                                <span @click="fulfillmentFilterIsVisible = true">
                                    Fulfillment Status
                                    <Icon type="ios-arrow-down"></Icon>
                                </span>

                                <!-- Dropdown Options -->
                                <DropdownMenu slot="list">
                                    
                                    <div class="p-2 pl-3 pr-5 text-left">

                                        <!-- Status Checkboxes -->
                                        <CheckboxGroup v-model="selectedFulfillmentStatuses">
                                            <Checkbox class="d-block" label="Fulfilled"></Checkbox>
                                            <Checkbox class="d-block" label="Unfulfilled"></Checkbox>
                                            <Checkbox class="d-block" label="Partially Fulfilled"></Checkbox>
                                        </CheckboxGroup>

                                        <div class="mt-4">

                                            <!-- Apply Filter Button -->
                                            <Button type="primary" @click="fulfillmentFilterIsVisible = false">Apply Filter(s)</Button>

                                        </div>

                                    </div>

                                </DropdownMenu>

                            </Dropdown>

                        </Button>

                        <!-- Fulfillment Status Button -->
                        <Button>More Filters</Button>

                    </ButtonGroup>

                </Col>

                <Col :span="6">

                    <!-- Save Filter Button -->
                    <Button class="ml-1">
                        <Icon type="md-star-outline" :size="20" />
                        <span>Saved</span>
                    </Button>

                    <!-- Sort Button -->
                    <Button class="ml-2">
                        <Icon type="ios-shuffle" :size="20" />
                        <span>Sort</span>
                    </Button>

                </Col>

            </Row>

            <!-- Store Orders -->
            <Table :columns="dynamicColumns" :data="localOrders"
                no-data-text="No orders found"
                :loading="isLoadingOrders"
                class="order-table">
            </Table>

        </Card>

    </div>

</template>

<script>
    
    /*  Buttons  */
    import basicButton from './../../../components/_common/buttons/basicButton.vue';

    /*  Loaders  */
    import Loader from './../../../components/_common/loaders/Loader.vue';  

    /* Expand Table Row  */
    import orderRowDropDown from './../show/main.vue';  

    import moment from 'moment';

    export default {
        props: {
            ordersUrl: {
                type: String,
                default: null
            },
            store: {
                type: Object,
                default: null
            }
        }, 
        components: { 
            basicButton, Loader, orderRowDropDown
        },
        data(){
            return {
                moment: moment,

                //  Orders
                localOrders: [],
                searchedOrder: null,
                isLoadingOrders: false,
                showColumnManager: false,
                selectedPaymentStatuses: [],
                paymentFilterIsVisible: false,
                fulfillmentFilterIsVisible: false,
                selectedFulfillmentStatuses: [],
                localOrdersUrl: this.ordersUrl || this.$route.params.ordersUrl,
                tableColumnsToShowByDefault:['Order #', 'Customer', 'Email', 'Phone', 'Date', 'Grand Total'],

                //  Filters
                selectedOrderStatuses:[],
 
            }
        },
        computed:{
            activeTab(){
                return this.$route.query.orderType || 'all';
            },
            dynamicColumns(status){ 
                
                var allowedColumns = [];
                
                //  Expand Arrow
                allowedColumns.push(
                {
                    type: 'expand',
                    width: 30,
                    render: (h, params) => {
                        return h(orderRowDropDown, {
                            props: {
                                order: params.row,
                                store: this.store
                            },
                            on: {
                                updated: (order) => {

                                    //  Update the row data
                                    this.$set(this.localOrders, params.index, order);

                                    for(var x=0; x < this.localOrders.length; x++){

                                        if( x == params.index){

                                            //  Automatically open the expandable data
                                            this.$set(this.localOrders[params.index], '_expanded', true);

                                        }else{

                                            //  Automatically close the expandable data
                                            this.$set(this.localOrders[x], '_expanded', false);

                                        }

                                    }
                                    
                                }
                            }
                        })
                    }
                });
                
                //  Status Icon
                allowedColumns.push(
                {
                    width: 40,
                    title: ' ',
                    align: 'center',
                    render: (h, params) => {
                        return h('Poptip', {
                                    style: {
                                        width: '100%',
                                        textAlign:'left'
                                    },
                                    props: {
                                        width: 350,
                                        wordWrap: true,
                                        trigger:'hover',
                                        placement: 'right',
                                        title: (params.row.status || {}).name,
                                        content: (params.row.status || {}).description,
                                    }
                                }, [
                                    h('Badge', {
                                        props: {
                                            status: this.getOrderStatusColor( (params.row.status || {}).name )
                                        },
                                        class: ['d-flex']
                                    })
                                ]);
                    }
                });
                
                //  Order #
                if(this.tableColumnsToShowByDefault.includes('Order #')){
                    allowedColumns.push(
                    {
                        title: 'Order #',
                        render: (h, params) => {
                            return h('span', (params.row.number));
                        }
                    });
                }
                
                //  Customer Details
                if(this.tableColumnsToShowByDefault.includes('Customer')){
                    allowedColumns.push(
                    {
                        title: 'Customer',
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
                                    placement: 'top-start',
                                    content: 'Customer: '+((params.row.billing_info || {}).name  || '...')
                                }
                            }, [
                                h('span', {
                                    class: ['cut-text']
                                }, ((params.row.billing_info || {}).name) || '...')
                            ])
                        }
                    });
                }
                
                //  Customer Email
                if(this.tableColumnsToShowByDefault.includes('Email')){
                    allowedColumns.push(
                    {
                        title: 'Email',
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
                                    placement: 'top-start',
                                    content: 'Email: '+((params.row.billing_info || {}).email  || '...')
                                }
                            }, [
                                h('span', {
                                    class: ['cut-text']
                                }, ((params.row.billing_info || {}).email) || '...')
                            ])
                        }
                    })
                }
                
                //  Customer Phones
                if(this.tableColumnsToShowByDefault.includes('Phone')){
                    allowedColumns.push(
                    {
                        title: 'Phone',
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
                                    placement: 'top-start',
                                    content: 'Phone: '+(((params.row.billing_info || {}).default_mobile || {}).full_number  || '...')
                                }
                            }, [
                                h('span', {
                                    class: ['cut-text']
                                }, (((params.row.billing_info || {}).default_mobile || {}).full_number  || '...'))
                            ])
                        }
                    })
                }
                
                //  Date
                if(this.tableColumnsToShowByDefault.includes('Date')){
                    allowedColumns.push(
                    {
                        title: 'Date',
                        sortable: true,
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
                                    placement: 'top-start',
                                    content: 'Date: '+ this.formatDate(params.row.created_at.date, true)
                                }
                            }, [
                                h('span', {
                                    class: ['cut-text']
                                }, this.formatDate(params.row.created_at.date))
                            ])
                        }
                    })
                }
                
                //  Grand Total
                if(this.tableColumnsToShowByDefault.includes('Grand Total')){
                    allowedColumns.push(
                    {
                        title: 'Total',
                        render: (h, params) => {
                            
                            var subTotal = (params.row.sub_total || 0);
                            var taxTotal = (params.row.grand_tax_total || 0);
                            var discountTotal = (params.row.grand_discount_total || 0);
                            var grandTotal = (params.row.grand_total || 0); 
                            var symbol = (params.row.currency || {}).symbol || (params.row.currency || {}).code;

                            return h('Poptip', {
                                style: {
                                    width: '100%',
                                    textAlign:'left'
                                },
                                props: {
                                    width: 280,
                                    wordWrap: true,
                                    trigger:'hover',
                                    placement: 'top-end',
                                    title: 'Breakdown'
                                },
                                class: ['breakdown-poptip']
                            }, [
                                h('span', {
                                    class: ['cut-text']
                                }, this.formatPrice(grandTotal, symbol) ),
                                h('List', {
                                        slot: 'content',
                                        props: {
                                            slot: 'content',
                                            size: 'small'
                                        }
                                    }, [
                                        h('ListItem', 'Sub Total: '+this.formatPrice(subTotal, symbol) ),
                                        h('ListItem', 'Tax Total: '+this.formatPrice(taxTotal, symbol) ),
                                        h('ListItem', 'Discount Total: '+this.formatPrice(discountTotal, symbol) ),
                                        h('ListItem', 'Grand Total: '+this.formatPrice(grandTotal, symbol) )
                                    ])
                            ])
                        }
                    })
                }
                
                //  Action
                allowedColumns.push(
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
                });

                return allowedColumns;
            }
        },
        methods: {
            getOrderStatusColor(status){
                if (['Open', 'Resumed', 'Undo Cancellation', 
                     'Undo Skip Payment', 'Undo Skip Delivery', 
                     'Undo Payment', 'Undo Delivery', 'Undo Completed'].includes(status)) {
                    return 'default';
                }else if(['Pending Payment', 'Pending Delivery'].includes(status)) {
                    return 'warning';
                }else if(['Paid', 'Processing'].includes(status)) {
                    return 'processing';
                }else if(['Cancelled', 'Failed Payment'].includes(status)) {
                    return 'error';
                /*}else if(['Delivered'].includes(status)) {
                    return 'order-delivered-status';
                }*/
                }else if(['Completed'].includes(status)) {
                    return 'success';
                }else{
                    return '';
                }
            },
            formatPrice(money, symbol) {
                let val = (money/1).toFixed(2).replace(',', '.');
                return symbol + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            },
            formatDate(date, withTime = false) {
                if( withTime ){

                    return this.moment(date).format('MMM DD YYYY @H:mmA');

                }else{

                    return this.moment(date).format('MMM DD YYYY');

                }
            },
            fetchOrders() {

                if( this.localOrdersUrl ){

                    //  Hold constant reference to the vue instance
                    const self = this;

                    //  Start loader
                    self.isLoadingOrders = true;

                    //  Console log to acknowledge the start of api process
                    console.log('Start getting orders...');

                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', this.localOrdersUrl)
                        .then(({data}) => {
                            
                            //  Console log the data returned
                            console.log(data);

                            //  Stop loader
                            self.isLoadingOrders = false;

                            //  Order the order data
                            self.localOrders = ((data || {})._embedded || {}).orders || [];

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
            //  Fetch the orders
            this.fetchOrders();
        }
    };
  
</script>