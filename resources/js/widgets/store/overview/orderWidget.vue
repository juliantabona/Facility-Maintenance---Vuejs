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
        width: 10px;
        height: 10px;
        background: #b3b3b3;
        border-radius: 10px;
    }

    .order-table >>> .order-open-status{
        background:#2d8cf0;
    }

    .order-table >>> .order-in-progress-status{
        background: #e8c207;
    }

    .order-table >>> .order-fail-status{
        background:#ff0000;
    }

    .order-table >>> .order-paid-status{
        background:#0de8c0;
    }

    .order-table >>> .order-delivered-status{
        background:#db0de8;
    }

    .order-table >>> .order-completed-status{
        background:#24d806;
    }

</style>

<template>

    <div>

        <!-- Loader -->
        <Loader v-if="isLoadingOrders" :loading="true" type="text" class="mt-5 text-left" theme="white">Loading orders...</Loader>
        
        <!-- No orders message -->
        <Alert v-if="!isLoadingOrders && !localOrders" type="info" :style="{ maxWidth: '250px' }" show-icon>No orders found</Alert>
        
        <Card v-if="!isLoadingOrders && localOrders" class="mb-3">
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
                    <!-- Add Order Button -->
                    <div class="clearfix">
                        <basicButton @click.native="$router.push({ name:'create-order' })" 
                                    size="large" class="float-right">
                                    <span>+ Add Order</span>
                        </basicButton>
                    </div>
                </Col>
            </Row>
        </Card>

        <CheckboxGroup  
            v-if="!isLoadingOrders && localOrders" 
            v-model="tableColumnsToShow" class="mb-3">
            <Checkbox label="Order #"></Checkbox>
            <Checkbox label="Customer"></Checkbox>
            <Checkbox label="Email"></Checkbox>
            <Checkbox label="Phone"></Checkbox>
            <Checkbox label="Date"></Checkbox>
            <Checkbox label="Grand Total"></Checkbox>
        </CheckboxGroup>

        <!-- Store Orders -->
        <Table v-if="!isLoadingOrders && localOrders" 
                :columns="dynamicColumns" :data="localOrders"
                class="order-table">
        </Table>

    </div>

</template>

<script>
    
    /*  Buttons  */
    import basicButton from './../../../components/_common/buttons/basicButton.vue';

    /*  Loaders  */
    import Loader from './../../../components/_common/loaders/Loader.vue';  

    /* Expand Table Row  */
    import orderRowDropDown from './orderRowDropDown.vue';  

    import moment from 'moment';

    export default {
        props: {
            storeId: {
                type: Number,
                default: null
            }
        }, 
        components: { 
            basicButton, Loader, orderRowDropDown
        },
        data(){
            return {
                moment: moment,

                localStoreId: this.storeId || this.$route.params.storeId,
                isLoadingStore: false,

                //  Orders
                localOrders: null,
                isLoadingOrders: false,

                selectedOrderStatuses:[],

                tableColumnsToShow:['Order #', 'Customer', 'Email', 'Phone', 'Date', 'Grand Total']
 
            }
        },
        computed:{
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
                                order: params.row
                            },
                            on: {
                                updated: (order) => {

                                    //  Update the row data
                                    this.$set(this.localOrders, params.index, order);

                                    //  Automatically open the expandable data
                                    this.$set(this.localOrders[params.index], '_expanded', true);
                                    
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
                                        width: 280,
                                        wordWrap: true,
                                        trigger:'hover',
                                        placement: 'right',
                                        title: (params.row.current_lifecycle_main_status || {}).title,
                                        content: (params.row.current_lifecycle_main_status || {}).description,
                                    }
                                }, [
                                    h('div', {
                                            class: ['order-status', this.getOrderStatusColor(params.row.current_lifecycle_main_status)]
                                        })
                                ]);
                    }
                });
                
                //  Order #
                if(this.tableColumnsToShow.includes('Order #')){
                    allowedColumns.push(
                    {
                        width: 80,
                        title: 'Order #',
                        render: (h, params) => {
                            return h('span', (params.row.number));
                        }
                    });
                }
                
                //  Customer Details
                if(this.tableColumnsToShow.includes('Customer')){
                    allowedColumns.push(
                    {
                        width: 150,
                        title: 'Customer',
                        render: (h, params) => {
                            return h('span', (
                                (params.row.billing_info || {}).name
                            ));
                        }
                    });
                }
                
                //  Customer Email
                if(this.tableColumnsToShow.includes('Email')){
                    allowedColumns.push(
                    {
                        width: 200,
                        title: 'Email',
                        render: (h, params) => {
                            return h('span', ((params.row.billing_info || {}).email) || '...');
                        }
                    });
                }
                
                //  Customer Phones
                if(this.tableColumnsToShow.includes('Phone')){
                    allowedColumns.push(
                    {
                        width: 130,
                        title: 'Phone',
                        render: (h, params) => {
                            return h('span', ( 
                                ((params.row.billing_info || {}).default_mobile || {}).full_number 
                            ));
                        }
                    });
                }
                
                //  Date
                if(this.tableColumnsToShow.includes('Date')){
                    allowedColumns.push(
                    {
                        width: 120,
                        title: 'Date',
                        sortable: true,
                        render: (h, params) => {
                            return h('span', this.formatDate(params.row.created_at.date));
                        }
                    });
                }
                
                //  Grand Total
                if(this.tableColumnsToShow.includes('Grand Total')){
                    allowedColumns.push(
                    {
                        width: 130,
                        title: 'Total',
                        key: 'grand_total',
                        sortable: true,
                        render: (h, params) => {
                            var grandTotal = (params.row.grand_total || 0) 
                            var symbol = (params.row.currency || {}).symbol || '';
                            return h('span', this.formatPrice(grandTotal, symbol) );
                        }
                    });
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
                     'Undo Payment', 'Undo Delivery', 'Undo Completed'].includes(status.title)) {
                    return 'order-open-status';
                }else if(['Pending Payment', 'Pending Delivery'].includes(status.title)) {
                    return 'order-in-progress-status';
                }else if(['Cancelled', 'Failed Payment'].includes(status.title)) {
                    return 'order-fail-status';
                }else if(['Paid'].includes(status.title)) {
                    return 'order-paid-status';
                }else if(['Delivered'].includes(status.title)) {
                    return 'order-delivered-status';
                }else if(['Completed'].includes(status.title)) {
                    return 'order-completed-status';
                }else{
                    return '';
                }
            },
            formatPrice(money, symbol) {
                let val = (money/1).toFixed(2).replace(',', '.');
                return symbol + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            },
            formatDate(date) {
                return this.moment(date).format('MMM DD YYYY');
            },
            fetchOrders() {

                if( this.localStoreId ){

                    //  Hold constant reference to the vue instance
                    const self = this;

                    //  Start loader
                    self.isLoadingOrders = true;

                    //  Console log to acknowledge the start of api process
                    console.log('Start getting orders...');

                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', '/api/orders?storeId='+this.localStoreId)
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