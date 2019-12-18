<style scoped>

    .order-card >>> .ivu-card-head{
        padding-bottom:0 !important;
    }

    .order-card >>> .order-menu {
        z-index: 1;
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

    .order-filter-dropdown >>> .ivu-select-dropdown {
        top: 30px !important;
        left: 0px !important;
    }

    .order-columns-dropdown >>> .ivu-select-dropdown {
        top: 30px !important;
        right: 32px !important;
        left: inherit !important;
    }

    .filter-tag-box{
        padding: 10px 5px 0 5px;
        border-top: 3px solid #e4b52e;
    }

    .order-table >>> .ivu-spin-fix {
        z-index: 1 !important;
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

    .order-table >>> thead tr th span{
        font-weight: bold;
    }

    .order-table >>> .ivu-checkbox-wrapper{
        margin: 0;
        padding: 0;
    }

    .order-table >>> .cancelled{
        text-decoration: line-through;
    }

</style>

<template>

    <div>

        <!-- If we have an active order to display -->
        <template v-if="activeOrderUrl">

            <!-- Show active order widget -->
            <showActiveOrderWidget :orderUrl="activeOrderUrl" :orders="localOrders"
                @changeOrder="activeOrderUrl = $event" 
                @goBack="fetchOrders()">
            </showActiveOrderWidget>

        </template>

        <!-- If we don't have an active order to display -->
        <template v-else>

            <!-- Order Heading / Add Order Button -->
            <Row class="border-bottom pb-3">

                <Col :span="4">

                    <!-- Order Heading -->
                    <h2 class="font-weight-bold pl-4">Orders</h2>

                </Col>

                <Col :span="20">
                    <div class="clearfix">
                        
                        <!-- Add Order Button -->
                        <basicButton @click.native="$router.push({ name:'create-order' })" 
                                    size="large" class="float-right">
                                    <span>+ Create Order</span>
                        </basicButton>

                    </div>
                </Col>

            </Row>

            <Card dis-hover class="order-card p-2">

                <!-- (1) Order Status [ All / Open / Archieved / Cancelled ] 
                    (2) Refresh Orders Button 
                -->
                <Row slot="title">

                    <!-- Order Status [ All / Open / Archieved / Draft / Cancelled ] -->
                    <Col :span="20">

                        <Menu mode="horizontal" theme="light" :active-name="activeOrderTab" class="order-menu"
                            @on-select="changeActiveOrderTab($event)">
                            <MenuItem name="all">
                                <span class="font-weight-bold">All</span>
                            </MenuItem>
                            <MenuItem name="open">
                                <Icon type="ios-thumbs-up-outline" :size="20" />
                                <span class="font-weight-bold">Open</span>
                            </MenuItem>
                            <MenuItem name="draft">
                                <Icon type="ios-list-box-outline" :size="20" />
                                <span class="font-weight-bold">Draft</span>
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

                    <!-- Refresh Orders Button -->
                    <Col :span="4">
                        <div class="clearfix">
                            <basicButton @click.native="fetchOrders()" 
                                        type="success" class="float-right"
                                        :disabled="isLoadingOrders">
                                        <Icon type="ios-refresh" :size="20"/>
                                        <span>Refresh</span>
                            </basicButton>
                        </div>
                    </Col>

                </Row>
                
                <!-- Order Search / Filter By Payment Status / Filter By Fulfillment Status / Sort -->
                <Row>

                    <Col :span="16">

                        <ButtonGroup>

                            <!-- Search Field -->
                            <Button class="p-0">
                                <Input v-model="searchedOrder" prefix="ios-search" placeholder="Search orders..." 
                                    clearable class="order-search-bar" style="width: 200px;" />
                            </Button>

                            <!-- Payment Status Button -->
                            <Button class="p-0">

                                <!-- Dropdown -->
                                <Dropdown trigger="click" placement="bottom-start">
                                    
                                    <!-- Title -->
                                    <span class="pr-2 pl-2">
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

                                        </div>

                                    </DropdownMenu>

                                </Dropdown>

                            </Button>

                            <!-- Fulfillment Status Button -->
                            <Button class="p-0">

                                <!-- Dropdown -->
                                <Dropdown trigger="click" placement="bottom-start">
                                    
                                    <!-- Title -->
                                    <span class="pr-2 pl-2">
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

                                        </div>

                                    </DropdownMenu>

                                </Dropdown>

                            </Button>

                            <!-- More Filters Button -->
                            <Button>
                                <Icon type="ios-funnel-outline" :size="18" />
                                <span>More Filters</span>
                            </Button>

                        </ButtonGroup>

                    </Col>

                    <Col :span="8" class="clearfix">

                        <!-- Columns Button -->
                        <Button class="p-0 float-right">

                            <!-- Dropdown -->
                            <Dropdown trigger="click" placement="bottom-end">
                                
                                <!-- Title -->
                                <span class="pr-2 pl-2">
                                    <span>Columns</span>
                                    <Icon type="ios-arrow-down"></Icon>
                                </span>

                                <!-- Dropdown Options -->
                                <DropdownMenu slot="list">
                                    
                                    <div class="p-2 pl-3 pr-5 text-left">

                                        <!-- Status Checkboxes -->
                                        <CheckboxGroup v-model="tableColumnsToShowByDefault">
                                            <Checkbox class="d-block" label="Order Selector"></Checkbox>
                                            <Checkbox class="d-block" label="Show Summary Arrow"></Checkbox>
                                            <Checkbox class="d-block" label="Payment Indicator"></Checkbox>
                                            <Checkbox class="d-block" label="Customer"></Checkbox>
                                            <Checkbox class="d-block" label="Email"></Checkbox>
                                            <Checkbox class="d-block" label="Phone"></Checkbox>
                                            <Checkbox class="d-block" label="Payment Status"></Checkbox>
                                            <Checkbox class="d-block" label="Fulfillment Status"></Checkbox>
                                            <Checkbox class="d-block" label="Date"></Checkbox>
                                            <Checkbox class="d-block" label="Sub Total"></Checkbox>
                                            <Checkbox class="d-block" label="Discount Total"></Checkbox>
                                            <Checkbox class="d-block" label="Tax Total"></Checkbox>
                                            <Checkbox class="d-block" label="Grand Total"></Checkbox>
                                        </CheckboxGroup>

                                    </div>

                                </DropdownMenu>

                            </Dropdown>

                        </Button>

                        <!-- Save Filters Button -->
                        <Button class="float-right mr-2">
                            <Icon type="md-star-outline" :size="20" />
                            <span>Save Filters</span>
                        </Button>

                    </Col>

                </Row>

                <!-- Filters Displayed As Closable Tags -->
                <Row v-if="selectedPaymentStatuses.length || selectedFulfillmentStatuses.length" class="filter-tag-box mt-2">

                    <Col :span="24">

                        <!-- Tags For Payment Filters -->
                        <Tag v-for="(status, i) in selectedPaymentStatuses" :key="status" :name="status" closable @on-close="removePaymentStatusTag(i)">
                            Payment {{ status.toLowerCase() }}
                        </Tag> 

                        <!-- Tags For Fulfillment Filters -->
                        <Tag v-for="(status, i) in selectedFulfillmentStatuses" :key="status" :name="status" closable @on-close="removeFulfillmentStatusTag(i)">
                            {{ status }}
                        </Tag> 

                    </Col>

                </Row>

                <!-- Selected Order(s) Actions / Order List -->
                <div class="mt-4">

                    <!-- Selected Order(s) Actions -->
                    <Row v-if="selectedOrders.length" class="mb-2">

                        <Col :span="16">

                            <ButtonGroup>

                                <!-- Number Of Selected Orders -->
                                <Button class="pr-4 pl-4">
                                    <Badge :count="selectedOrders.length" type="warning" class="mr-1"></Badge> 
                                    <span>Selected</span>
                                </Button>

                                <!-- Actions Button -->
                                <Button>

                                    <!-- Dropdown -->
                                    <Dropdown trigger="click">
                                        
                                        <!-- Title -->
                                        <span>
                                            Actions
                                            <Icon type="ios-arrow-down"></Icon>
                                        </span>

                                        <!-- Dropdown Options -->
                                        <DropdownMenu slot="list" class="text-left">

                                            <DropdownItem>Fulfill orders</DropdownItem>
                                            <DropdownItem>Capture payments</DropdownItem>

                                            <Divider class="mt-1 mb-1"></Divider>

                                            <DropdownItem>Archive orders</DropdownItem>
                                            <DropdownItem>Unarchive orders</DropdownItem>

                                            <Divider class="mt-1 mb-1"></Divider>

                                            <DropdownItem>Add tags</DropdownItem>
                                            <DropdownItem>Remove tags</DropdownItem>

                                        </DropdownMenu>

                                    </Dropdown>

                                </Button>
                            
                            </ButtonGroup>

                        </Col>

                    </Row>

                    <!-- Store Orders -->
                    <Table :columns="dynamicColumns" :data="localOrders"
                        no-data-text="No orders found" :loading="isLoadingOrders"
                        @on-select-all-cancel="manageSelectedAllOrders"
                        @on-select-all="manageSelectedAllOrders"
                        @on-select-cancel="manageSelectedOrder"
                        @on-select="manageSelectedOrder"
                        class="order-table">
                    </Table>

                    <!-- Pagination -->
                    <div class="clearfix mt-4">
                        <div style="float: right;">
                            <Page :total="1000" :current="1" @on-change="true"></Page>
                        </div>
                    </div>

                </div>

            </Card>

        </template>

    </div>

</template>

<script>
    
    /*  Buttons  */
    import basicButton from './../../../components/_common/buttons/basicButton.vue';

    /*  Loaders  */
    import Loader from './../../../components/_common/loaders/Loader.vue';  
    
    /*  Active Order Widget  */
    import showActiveOrderWidget from './../show/main.vue';

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
            basicButton, Loader, orderRowDropDown, showActiveOrderWidget
        },
        data(){
            return {
                moment: moment,
                //  Current store orders
                localOrders: [],
                //  Selected orders
                selectedOrders: [],
                //  Active order url
                activeOrderUrl: null,
                //  Searched order value
                searchedOrder: null,
                //  LLoading orders status
                isLoadingOrders: false,
                //  Get the url used to fetch the store orders
                localOrdersUrl: this.ordersUrl,
                //  Check if we have any payment filters stored on the url query
                selectedPaymentStatuses: this.getSelectedPaymentStatuses(),
                //  Check if we have any fulfillment filters stored on the url query
                selectedFulfillmentStatuses: this.getSelectedFulfillmentStatuses(),
                //  Set the default columns to show
                tableColumnsToShowByDefault:[
                    'Order Selector', 'Show Summary Arrow', 'Order #', 'Customer', 
                    'Payment Status', 'Fulfillment Status', 'Date', 'Grand Total'
                ],
            }
        },
        watch: {
            /** Watch for changes on the selected payment statuses. The reason we want to watch for changes 
             *  on this value is so that we can use the last recorded value to update the url query. This is 
             *  so that when the user manually refreshes the browser when can always know which payment 
             *  statuses they last selected. We can use this information to reselect those exact 
             *  filters they had selected
             */  
            selectedPaymentStatuses: {
                handler: function (val, oldVal) {

                    this.updateUrlFilters();

                }
            },

            /** Watch for changes on the selected fulfillment statuses. The reason we want to watch for changes 
             *  on this value is so that we can use the last recorded value to update the url query. This is 
             *  so that when the user manually refreshes the browser when can always know which fulfillment 
             *  statuses they last selected. We can use this information to reselect those exact 
             *  filters they had selected
             */  
            selectedFulfillmentStatuses: {
                handler: function (val, oldVal) {

                    this.updateUrlFilters();

                }
            }

        },
        computed:{
            //  Get the active order tab e.g All / Archieved / Cancelled
            activeOrderTab(){
                return this.$route.query.activeOrderTab || 'all';
            },
            dynamicColumns(status){ 
                
                var allowedColumns = [];

                //  Order Selector
                if(this.tableColumnsToShowByDefault.includes('Order Selector')){
                    allowedColumns.push({
                        type: 'selection',
                        align: 'center',
                        width: 60
                    });
                }

                //  Show Summary Arrow (Expand Arrow)
                if(this.tableColumnsToShowByDefault.includes('Show Summary Arrow')){
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
                }
                    
                //  Payment Indicator
                if(this.tableColumnsToShowByDefault.includes('Payment Indicator')){
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
                                                status: this.getPaymentStatusColor(params.row.payment_status.name)
                                            },
                                            class: ['d-flex']
                                        })
                                    ]);
                        }
                    });
                }
                
                //  Order #
                if(this.tableColumnsToShowByDefault.includes('Order #')){
                    allowedColumns.push(
                    {
                        title: 'Order #',
                        sortable: true,
                        render: (h, params) => {
                            return h('span', {
                                class: [(params.row.status.name == 'Cancelled' ? 'text-danger' : '')],
                                on: {
                                    click: () => {

                                        this.activeOrderUrl = ((params.row._links || {}).self || {}).href;
                                    }
                                }
                            }, [
                                h('Button', {
                                    props: {
                                        type: 'text',
                                        ghost: true
                                    },
                                    class: ['text-dark']
                                }, (params.row.number) || '...')
                            ]);
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
                                    class: ['cut-text', 'text-capitalize', (params.row.status.name == 'Cancelled' ? 'text-danger' : '')]
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
                                    class: ['cut-text', (params.row.status.name == 'Cancelled' ? 'text-danger' : '')]
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
                                    class: ['cut-text', (params.row.status.name == 'Cancelled' ? 'text-danger' : '')]
                                }, (((params.row.billing_info || {}).default_mobile || {}).full_number  || '...'))
                            ])
                        }
                    })
                }
                
                //  Payment Status
                if(this.tableColumnsToShowByDefault.includes('Payment Status')){
                    allowedColumns.push(
                    {
                        width: 180,
                        title: 'Payment',
                        render: (h, params) => {
                            //  Poptip
                            return h('Poptip', {
                                style: {
                                    width: '100%',
                                    textAlign:'left'
                                },
                                props: {
                                    width: 350,
                                    wordWrap: true,
                                    trigger:'hover',
                                    placement: 'top-start',
                                    content: 'Status: '+(params.row.payment_status.description  || '...')
                                }
                            }, [
                                //  Badge
                                h('Tag', {
                                    props: {
                                        type: 'dot',
                                        color: this.getPaymentStatusColor(params.row.payment_status.name)
                                    },
                                    class: ['rounded-status-tag']
                                }, [
                                    //  Fulfillment status text
                                    h('span', {
                                        class: ['cut-text', 'text-capitalize']
                                    }, params.row.payment_status.name)
                                ])
                            ])
                        }
                    })
                }
                
                //  Fulfillment Status
                if(this.tableColumnsToShowByDefault.includes('Fulfillment Status')){
                    allowedColumns.push(
                    {
                        width: 170,
                        title: 'Fulfillment',
                        render: (h, params) => {
                            //  Poptip
                            return h('Poptip', {
                                style: {
                                    width: '100%',
                                    textAlign:'left'
                                },
                                props: {
                                    width: 350,
                                    wordWrap: true,
                                    trigger:'hover',
                                    placement: 'top-start',
                                    content: 'Status: '+(params.row.fulfillment_status.description  || '...')
                                }
                            }, [
                                //  Badge
                                h('Tag', {
                                    props: {
                                        type: 'dot',
                                        color: this.getFulfillmentStatusColor(params.row.fulfillment_status.name)
                                    },
                                    class: ['rounded-status-tag']
                                }, [
                                    //  Fulfillment status text
                                    h('span', {
                                        class: ['cut-text', 'text-capitalize']
                                    }, params.row.fulfillment_status.name)
                                ])
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
                                    class: ['cut-text', (params.row.status.name == 'Cancelled' ? 'text-danger' : '')]
                                }, this.formatDate(params.row.created_at.date))
                            ])
                        }
                    })
                }
                
                //  Sub Total
                if(this.tableColumnsToShowByDefault.includes('Sub Total')){
                    allowedColumns.push(
                    {
                        width: 150,
                        title: 'Sub Total',
                        sortable: true,
                        render: (h, params) => {
                            
                            var subTotal = (params.row.sub_total || 0);
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
                                    class: ['cut-text', (params.row.status.name == 'Cancelled' ? 'cancelled text-danger' : '')]
                                }, this.formatPrice(subTotal, symbol) ),
                                h('List', {
                                        slot: 'content',
                                        props: {
                                            slot: 'content',
                                            size: 'small'
                                        }
                                    }, [
                                        h('ListItem', {
                                            class: ['font-weight-bold']
                                        }, 'Sub Total: '+this.formatPrice(subTotal, symbol) )
                                    ])
                            ])
                        }
                    })
                }
                
                //  Discount Total
                if(this.tableColumnsToShowByDefault.includes('Discount Total')){
                    allowedColumns.push(
                    {
                        width: 180,
                        title: 'Discount Total',
                        sortable: true,
                        render: (h, params) => {
                            
                            var itemDiscountTotal = (params.row.item_discount_total || 0);
                            var cartDiscountTotal = (params.row.global_discount_total || 0);
                            var discountTotal = (params.row.grand_discount_total || 0);
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
                                    class: ['cut-text', (params.row.status.name == 'Cancelled' ? 'cancelled text-danger' : '')]
                                }, this.formatPrice(discountTotal, symbol) ),
                                h('List', {
                                        slot: 'content',
                                        props: {
                                            slot: 'content',
                                            size: 'small'
                                        }
                                    }, [
                                        h('ListItem', 'Item Discount Total: '+this.formatPrice(itemDiscountTotal, symbol) ),
                                        h('ListItem', 'Cart Discount Total: '+this.formatPrice(cartDiscountTotal, symbol) ),
                                        h('ListItem', {
                                            class: ['font-weight-bold']
                                        }, 'Overall Total: '+this.formatPrice(discountTotal, symbol) )
                                    ])
                            ])
                        }
                    })
                }
                
                //  Tax Total
                if(this.tableColumnsToShowByDefault.includes('Tax Total')){
                    allowedColumns.push(
                    {
                        title: 'Tax Total',
                        sortable: true,
                        render: (h, params) => {
                            
                            var itemTaxTotal = (params.row.item_tax_total || 0);
                            var cartTaxTotal = (params.row.global_tax_total || 0);
                            var taxTotal = (params.row.grand_tax_total || 0);
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
                                    class: ['cut-text', (params.row.status.name == 'Cancelled' ? 'cancelled text-danger' : '')]
                                }, this.formatPrice(taxTotal, symbol) ),
                                h('List', {
                                        slot: 'content',
                                        props: {
                                            slot: 'content',
                                            size: 'small'
                                        }
                                    }, [
                                        h('ListItem', 'Item Tax Total: '+this.formatPrice(itemTaxTotal, symbol) ),
                                        h('ListItem', 'Cart Tax Total: '+this.formatPrice(cartTaxTotal, symbol) ),
                                        h('ListItem', {
                                            class: ['font-weight-bold']
                                        }, 'Overall Total: '+this.formatPrice(taxTotal, symbol) )
                                    ])
                            ])
                        }
                    })
                }
                
                //  Grand Total
                if(this.tableColumnsToShowByDefault.includes('Grand Total')){
                    allowedColumns.push(
                    {
                        title: 'Grand Total',
                        sortable: true,
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
                                    class: ['cut-text', (params.row.status.name == 'Cancelled' ? 'cancelled text-danger' : '')]
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
                                        h('ListItem', {
                                            class: ['font-weight-bold']
                                        },'Grand Total: '+this.formatPrice(grandTotal, symbol) )
                                    ])
                            ])
                        }
                    })
                }

                return allowedColumns;
            }
        },
        methods: {
            updateUrlFilters(){
                
                /** Get the updated value of the selected payment & fulfillment statuses. We can use this value to update the  
                 *  queries on the URL so that we can always know the exact selected payment & fulfillment statuses even 
                 *  after the browser is refreshed since we can catch that query values on the:
                 * 
                 *  getSelectedPaymentStatuses() and getSelectedFulfillmentStatuses() methods
                 * 
                 *  First we need to get the url of the route. To get this value we can generate a new url by revolving a  
                 *  named route. Resolving means that we build a complete url  resource of a route. We need to make sure
                 *  that we include the queries we want to change on the named route we want to resolve so that the url 
                 *  we get has the updated query values. Once we resolve the named route e.g "stores" then we can access 
                 *  its href and get the url we need. After we get the url we can change the actual url value of our 
                 *  browser by using the history.replaceState() method. The problem we have is that when we use:
                 *  
                 *  this.$router.replace({name: 'stores', query: { ... }}) or
                 *  this.$router.push({name: 'stores', query: { ... }})
                 * 
                 *  we are able to change the url but the page refreshes which is not a desired result. We want the 
                 *  page not to refresh but have its url updated. To do this we use the history.replaceState() 
                 *  method which will do exactly that. The method takes multiple parameters but we only need
                 *  to update the third parameter with our url.
                 */  

                var url = this.$router.resolve({name: 'stores', query: {

                    //  Get all the previous url queries
                    ...this.$route.query, 

                    //  Add / Update our paymentFilters query
                    fulfillmentFilters: this.selectedFulfillmentStatuses.join(),

                    //  Add / Update our fulfillmentFilters query
                    paymentFilters: this.selectedPaymentStatuses.join()

                }}).href;

                history.pushState({}, null, url);

                //  Re-fetch the orders
                this.fetchOrders();
            },
            //  Check if we have any payment filters stored on the url query
            getSelectedPaymentStatuses(){

                /** Get the query value from the url. If we don't have a value default to an empty string ''. 
                 *  If we do have a value we expect it to be a comma separated string. We need to explode the 
                 *  string into an array. If we have nothing to split (its an empty string or equal '') then
                 *  we finally deafult to an empty array.
                 */  
                if( this.$route.query.paymentFilters ){
                    return (this.$route.query.paymentFilters).split(',');
                }

                return [];
            },
            //  Check if we have any fulfillment filters stored on the url query
            getSelectedFulfillmentStatuses(){

                /** Get the query value from the url. If we don't have a value default to an empty string ''. 
                 *  If we do have a value we expect it to be a comma separated string. We need to explode the 
                 *  string into an array. If we have nothing to split (its an empty string or equal '') then
                 *  we finally deafult to an empty array.
                 */  
                if( this.$route.query.fulfillmentFilters ){
                    return (this.$route.query.fulfillmentFilters).split(',');
                }

                return [];
            },
            changeActiveOrderTab(activeOrderTabName){

                //  Update the url query with the active tab name
                this.$router.replace({name: 'stores', query: {

                    //  Get all the current url queries
                    ...this.$route.query, 

                    //  Add / Update our query
                    activeOrderTab: activeOrderTabName

                }});

                this.updateUrlFilters();

            },
            removePaymentStatusTag(index){

                //  Remove the payment status
                this.selectedPaymentStatuses.splice(index, 1);

            },
            removeFulfillmentStatusTag(index){

                //  Remove the fulfillment status
                this.selectedFulfillmentStatuses.splice(index, 1);

            },
            manageSelectedAllOrders(selection){
                this.selectedOrders = selection;
            },
            manageSelectedOrder(selection, row){
                this.selectedOrders = selection;
            },
            getPaymentStatusColor(status){
                if (['Unpaid'].includes(status)) {
                    return 'default';
                }else if(['Pending', 'Partially paid'].includes(status)) {
                    return 'warning';
                }else if(['Failed Payment'].includes(status)) {
                    return 'error';
                }else if(['Authorized', 'Refunded', 'Partially Refunded', 'Paid', 'Voided'].includes(status)) {
                    return 'success';
                }else{
                    return '';
                }
            },
            getFulfillmentStatusColor(status){
                if (['Unfulfilled'].includes(status)) {
                    return 'default';
                }else if(['Partially Fulfilled'].includes(status)) {
                    return 'warning';
                }else if(['Fulfilled'].includes(status)) {
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

                    //  Make sure we are not displaying any order
                    self.activeOrderUrl = null;

                    //  Console log to acknowledge the start of api process
                    console.log('Start getting orders...');

                    let url = this.localOrdersUrl + '?';

                    /** Get all the status of the used to filter the orders
                     *  We combine as the available statues e.g:
                     * 
                     *  General Statuses = Open / Archieved / Cancelled e.t.c
                     * 
                     *  Payment Statuses = Paid / Unpaid / Pending, e.t.c
                     * 
                     *  Fulfillment Statuses = Fulfilled / Unfulfilled / Partially Fulfilled e.t.c
                     * 
                     *  All the statuses are separated into arrays e.g:
                     * 
                     *  generalStatuses = ['Open']
                     *  selectedPaymentStatuses = ['Paid', 'Unpaid', 'Pending']
                     *  selectedFulfillmentStatuses = ['Fulfilled', 'Unfulfilled', 'Partially Fulfilled']
                     * 
                     *  We need to join them into one array with values separated by a comma e.g:
                     * 
                     *  let status = ['Paid', 'Unpaid', 'Pending', 'Fulfilled', 'Unfulfilled', 'Partially Fulfilled']
                     * 
                    */
                    let status = (this.activeOrderTab != 'all' ? this.activeOrderTab : '');
                    let paymentStatus = this.selectedPaymentStatuses.join();
                    let fulfillmentStatus = this.selectedFulfillmentStatuses.join();

                    //  Append the filter by status values
                    url += 'status=' + status;
                    url += '&paymentStatus=' + paymentStatus;
                    url += '&fulfillmentStatus=' + fulfillmentStatus;

                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', url)
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