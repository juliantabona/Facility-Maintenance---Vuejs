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
        
        <!-- Order Filter & Create Button -->
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

        <!-- Table Column Checkboxes -->
        <CheckboxGroup  
            v-if="!isLoadingOrders && localOrders" 
            v-model="tableColumnsToShowByDefault" class="mb-3">
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
            ordersUrl: {
                type: String,
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
                localOrders: null,
                isLoadingOrders: false,
                localOrdersUrl: this.ordersUrl || this.$route.params.ordersUrl,
                tableColumnsToShowByDefault:['Order #', 'Customer', 'Email', 'Phone', 'Date', 'Grand Total'],

                //  Filters
                selectedOrderStatuses:[],
 
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
                                            class: ['order-status', this.getOrderStatusColor( (params.row.current_lifecycle_main_status || {}) )]
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
                            return h('span', {
                                class: ['cut-text']
                            }, this.formatDate(params.row.created_at.date));
                        }
                    });
                }
                
                //  Grand Total
                if(this.tableColumnsToShowByDefault.includes('Grand Total')){
                    allowedColumns.push(
                    {
                        title: 'Total',
                        render: (h, params) => {
                            
                            var subTotal = (params.row.sub_total || 0);
                            var taxTotal = (params.row.tax_total || 0);
                            var discountTotal = (params.row.discount_total || 0);
                            var grandTotal = (params.row.grand_total || 0); 
                            var symbol = (params.row.currency || {}).symbol || '';

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