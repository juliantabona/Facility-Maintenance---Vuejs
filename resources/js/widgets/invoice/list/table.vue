<style scoped>

    .invoice-card >>> .ivu-card-head{
        padding-bottom:0 !important;
    }

    .invoice-card >>> .invoice-menu {
        z-index: 1;
        height: 35px;
        line-height: 30px;
        background: transparent;
    }

    .invoice-card >>> .invoice-menu:after {
        background: transparent;
    }

    .invoice-card >>> .invoice-menu li {
        padding: 0 15px;
        font-size: 12px !important;
    }

    .invoice-search-bar >>> input {
        height: 30px;
        border: none;
    }

    .invoice-filter-dropdown >>> .ivu-select-dropdown {
        top: 30px !important;
        left: 0px !important;
    }

    .invoice-columns-dropdown >>> .ivu-select-dropdown {
        top: 30px !important;
        right: 32px !important;
        left: inherit !important;
    }

    .filter-tag-box{
        padding: 10px 5px 0 5px;
        border-top: 3px solid #e4b52e;
    }

    .invoice-table >>> .ivu-spin-fix {
        z-index: 1 !important;
    }

    .invoice-table >>> .breakdown-poptip .ivu-poptip-body{
        padding: 0 16px;
    }

    .invoice-table >>> .breakdown-poptip .ivu-list-container{
        margin-top: -20px !important;
        margin-bottom: -20px !important;
    }

    .invoice-table, .invoice-table >>> .ivu-table{
        overflow: inherit !important;
    }

    .invoice-table >>> thead tr th span{
        font-weight: bold;
    }

    .invoice-table >>> .ivu-checkbox-wrapper{
        margin: 0;
        padding: 0;
    }

    .invoice-table >>> .cancelled{
        text-decoration: line-through;
    }

</style>

<template>

    <div>

        <!-- If we have an active Invoice to display -->
        <template v-if="activeInvoiceUrl">

            <!-- Show active Invoice widget -->
            <showActiveInvoiceWidget :InvoiceUrl="activeInvoiceUrl" :invoices="localInvoices"
                @changeInvoice="activeInvoiceUrl = $event" 
                @goBack="fetchInvoices()">
            </showActiveInvoiceWidget>

        </template>

        <!-- If we don't have an active Invoice to display -->
        <template v-else>

            <Card dis-hover class="invoice-card p-2">

                <!-- (1) Invoice Status [ All / Open / Archieved / Cancelled ] 
                    (2) Refresh Invoices Button 
                -->
                <Row slot="title" class="mb-2">

                    <!-- Invoice Status [ All / Open / Archieved / Draft / Cancelled ] -->
                    <Col :span="20">

                        <!-- Custom Heading -->
                        <h5 v-if="title" :class="'d-block font-weight-bold'+( showFilterAndSortingOptions ? ' mb-2 mt-2' : '')">
                            {{ title }}
                        </h5>

                        <Menu v-if="showFilterAndSortingOptions" 
                              mode="horizontal" theme="light" :active-name="activeInvoiceTab"
                              class="invoice-menu" @on-select="changeActiveInvoiceTab($event)">
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

                    <!-- Refresh Invoices Button -->
                    <Col :span="4">
                        <div class="clearfix">
                            <basicButton @click.native="fetchInvoices()" 
                                        type="success" class="float-right"
                                        :disabled="isLoadingInvoices">
                                        <Icon type="ios-refresh" :size="20"/>
                                        <span>Refresh</span>
                            </basicButton>
                        </div>
                    </Col>

                </Row>
                
                <div v-if="showFilterAndSortingOptions" class="mb-4">

                    <!-- Invoice Search / Filter By Payment Status / Sort -->
                    <Row>

                        <Col :span="16">

                            <ButtonGroup>

                                <!-- Search Field -->
                                <Button class="p-0">
                                    <Input v-model="searchedInvoice" prefix="ios-search" placeholder="Search invoices..." 
                                        clearable class="invoice-search-bar" style="width: 200px;" />
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
                                                    <Checkbox class="d-block" label="Partially Paid"></Checkbox>
                                                    <Checkbox class="d-block" label="Pending"></Checkbox>
                                                    <Checkbox class="d-block" label="Refunded"></Checkbox>
                                                    <Checkbox class="d-block" label="Unpaid"></Checkbox>
                                                    <Checkbox class="d-block" label="Voided"></Checkbox>
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
                                <Dropdown trigger="hover" placement="bottom-end">
                                    
                                    <!-- Title -->
                                    <DropdownItem>
                                        More
                                        <Icon type="ios-arrow-down"></Icon>
                                    </DropdownItem>
                                    <DropdownMenu slot="list">

                                        <!-- Sort Menu Dropdown -->
                                        <DropdownItem>

                                            <!-- Sort Dropdown -->
                                            <Dropdown trigger="hover" placement="left-start">
                                                
                                                <!-- Title -->
                                                <DropdownItem>
                                                    <Icon type="ios-arrow-back" />
                                                    Sort
                                                </DropdownItem>

                                                <!-- Sort Options -->
                                                <DropdownMenu slot="list" style="width: 180px;">

                                                    <DropdownItem>Invoice number (ascending)</DropdownItem>
                                                    <DropdownItem>Invoice number (descending)</DropdownItem>
                                                    <DropdownItem>Date (oldest first)</DropdownItem>
                                                    <DropdownItem>Date (newest first)</DropdownItem>
                                                    <DropdownItem>Customer name (A-Z)</DropdownItem>
                                                    <DropdownItem>Customer name (Z-A)</DropdownItem>
                                                    <DropdownItem>Payment status (A-Z)</DropdownItem>
                                                    <DropdownItem>Payment status (Z-A)</DropdownItem>
                                                    <DropdownItem>Total price (low to high)</DropdownItem>
                                                    <DropdownItem>Total price (high to low)</DropdownItem>
                                                    
                                                </DropdownMenu>

                                            </Dropdown>
                                            
                                            
                                        </DropdownItem>

                                        <!-- Columns Menu Dropdown -->
                                        <DropdownItem>

                                            <!-- Columns Dropdown -->
                                            <Dropdown trigger="hover" placement="left-start">
                                                
                                                <!-- Title -->
                                                <DropdownItem>
                                                    <Icon type="ios-arrow-back" />
                                                    Columns
                                                </DropdownItem>

                                                <!-- Column Options -->
                                                <DropdownMenu slot="list" style="width: 180px;">

                                                    <div class="p-2 pl-3 pr-5 text-left">

                                                        <!-- Status Checkboxes -->
                                                        <CheckboxGroup v-model="tableColumnsToShowByDefault">
                                                            <Checkbox class="d-block" label="Invoice Selector"></Checkbox>
                                                            <Checkbox class="d-block" label="Show Summary Arrow"></Checkbox>
                                                            <Checkbox class="d-block" label="Customer"></Checkbox>
                                                            <Checkbox class="d-block" label="Email"></Checkbox>
                                                            <Checkbox class="d-block" label="Phone"></Checkbox>
                                                            <Checkbox class="d-block" label="Payment Status"></Checkbox>
                                                            <Checkbox class="d-block" label="Date"></Checkbox>
                                                            <Checkbox class="d-block" label="Sub Total"></Checkbox>
                                                            <Checkbox class="d-block" label="Discount Total"></Checkbox>
                                                            <Checkbox class="d-block" label="Tax Total"></Checkbox>
                                                            <Checkbox class="d-block" label="Grand Total"></Checkbox>
                                                        </CheckboxGroup>

                                                    </div>
                                                    
                                                </DropdownMenu>

                                            </Dropdown>
                                            
                                            
                                        </DropdownItem>

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
                    <Row v-if="selectedPaymentStatuses.length" class="filter-tag-box mt-2">

                        <Col :span="24">

                            <!-- Tags For Payment Filters -->
                            <Tag v-for="(status, i) in selectedPaymentStatuses" :key="status" :name="status" closable @on-close="removePaymentStatusTag(i)">
                                Payment {{ status.toLowerCase() }}
                            </Tag>

                        </Col>

                    </Row>
                    
                </div>

                <!-- Selected Invoice(s) Actions / Invoice List -->
                <div>

                    <!-- Selected Invoice(s) Actions -->
                    <Row v-if="selectedInvoices.length" class="mb-2">

                        <Col :span="16">

                            <ButtonGroup>

                                <!-- Number Of Selected Invoices -->
                                <Button class="pr-4 pl-4">
                                    <Badge :count="selectedInvoices.length" type="warning" class="mr-1"></Badge> 
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

                                            <DropdownItem>Capture payments</DropdownItem>

                                            <Divider class="mt-1 mb-1"></Divider>

                                            <DropdownItem>Archive invoices</DropdownItem>
                                            <DropdownItem>Unarchive invoices</DropdownItem>

                                            <Divider class="mt-1 mb-1"></Divider>

                                            <DropdownItem>Add tags</DropdownItem>
                                            <DropdownItem>Remove tags</DropdownItem>

                                        </DropdownMenu>

                                    </Dropdown>

                                </Button>
                            
                            </ButtonGroup>

                        </Col>

                    </Row>

                    <!-- Store Invoices -->
                    <Table :columns="dynamicColumns" :data="localInvoices"
                        no-data-text="No invoices found" :loading="isLoadingInvoices"
                        @on-select-all-cancel="manageSelectedAllInvoices"
                        @on-select-all="manageSelectedAllInvoices"
                        @on-select-cancel="manageSelectedInvoice"
                        @on-select="manageSelectedInvoice"
                        class="invoice-table">
                    </Table>

                    <!-- Pagination -->
                    <div v-if="showPaginationOptions" class="clearfix mt-4">
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

    /*  Status Badges  */
    import invoicePaymentStatusBadge from './../../../components/_common/statuses/invoicePaymentStatusBadge.vue';  
    
    /*  Active Invoice Widget  */
    import showActiveInvoiceWidget from './../show/main.vue';

    /* Expand Table Row  */
    import invoiceRowDropDown from './../show/main.vue';  

    import moment from 'moment';

    export default {
        props: {
            invoicesUrl: {
                type: String,
                default: null
            },
            showFilterAndSortingOptions: {
                type: Boolean,
                default: function(){
                    return true
                }
            },
            showPaginationOptions: {
                type: Boolean,
                default: function(){
                    return true
                }
            },
            title: {
                type: String,
                default: null
            }
        }, 
        components: { 
            basicButton, Loader, invoicePaymentStatusBadge, showActiveInvoiceWidget, invoiceRowDropDown
        },
        data(){
            return {
                moment: moment,
                //  Current store invoices
                localInvoices: [],
                //  Selected invoices
                selectedInvoices: [],
                //  Active invoice url
                activeInvoiceUrl: null,
                //  Searched invoice value
                searchedInvoice: null,
                //  LLoading invoices status
                isLoadingInvoices: false,
                //  Get the url used to fetch the store invoices
                localInvoicesUrl: this.invoicesUrl,
                //  Check if we have any payment filters stored on the url query
                selectedPaymentStatuses: this.getSelectedPaymentStatuses(),
                //  Set the default columns to show
                tableColumnsToShowByDefault:[
                    'Invoice Selector', 'Show Summary Arrow', 'Invoice #', 'Customer', 
                    'Payment Status', 'Date', 'Grand Total'
                ],
            }
        },
        watch: {
            /** Watch for changes on the invoices url. If this value changes we need to 
             *  refetch all the invoices
             */  
            invoicesUrl: {
                handler: function (val, oldVal) {

                    //  Get the updated invoices url
                    this.localInvoicesUrl = val;

                    //  Refetch the invoices
                    this.fetchInvoices();

                }
            },
            
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
            }

        },
        computed:{
            //  Get the active invoice tab e.g All / Archieved / Cancelled
            activeInvoiceTab(){
                return this.$route.query.activeInvoiceTab || 'all';
            },
            dynamicColumns(status){ 
                
                var allowedColumns = [];

                //  Invoice Selector
                if(this.tableColumnsToShowByDefault.includes('Invoice Selector')){
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
                            return h(invoiceRowDropDown, {
                                props: {
                                    invoice: params.row
                                },
                                on: {
                                    updated: (invoice) => {

                                        //  Update the row data
                                        this.$set(this.localInvoices, params.index, invoice);

                                        for(var x=0; x < this.localInvoices.length; x++){

                                            if( x == params.index){

                                                //  Automatically open the expandable data
                                                this.$set(this.localInvoices[params.index], '_expanded', true);

                                            }else{

                                                //  Automatically close the expandable data
                                                this.$set(this.localInvoices[x], '_expanded', false);

                                            }

                                        }
                                        
                                    }
                                }
                            })
                        }
                    });
                }
                
                //  Invoice #
                if(this.tableColumnsToShowByDefault.includes('Invoice #')){
                    allowedColumns.push(
                    {
                        title: 'Invoice #',
                        sortable: true,
                        render: (h, params) => {
                            return h('span', {
                                class: [(params.row.status.name == 'Cancelled' ? 'text-danger' : '')],
                                on: {
                                    click: () => {

                                        this.activeInvoiceUrl = ((params.row._links || {}).self || {}).href;
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
                            //  Payment Status Badge
                            return h(invoicePaymentStatusBadge, {
                                props: {
                                    status: params.row.status
                                }
                            })
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
                        width: 180,
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
                
                /** Get the updated value of the selected payment statuses. We can use this value to update the queries
                 *  on the URL so that we can always know the exact selected payment statuses even after the browser is 
                 *  refreshed since we can catch that query values on the:
                 * 
                 *  getSelectedPaymentStatuses() methods
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
                    paymentFilters: this.selectedPaymentStatuses.join()

                }}).href;

                history.pushState({}, null, url);

                //  Re-fetch the invoices
                this.fetchInvoices();
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
            changeActiveInvoiceTab(activeInvoiceTabName){

                //  Update the url query with the active tab name
                this.$router.replace({name: 'stores', query: {

                    //  Get all the current url queries
                    ...this.$route.query, 

                    //  Add / Update our query
                    activeInvoiceTab: activeInvoiceTabName

                }});

                this.updateUrlFilters();

            },
            removePaymentStatusTag(index){

                //  Remove the payment status
                this.selectedPaymentStatuses.splice(index, 1);

            },
            manageSelectedAllInvoices(selection){
                this.selectedInvoices = selection;
            },
            manageSelectedInvoice(selection, row){
                this.selectedInvoices = selection;
            },
            formatPrice(money, symbol) {
                let val = (money/1).toFixed(2).replace(',', '.');
                return (symbol ? symbol : '') + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            },
            formatDate(date, withTime = false) {
                if( withTime ){

                    return this.moment(date).format('MMM DD YYYY @H:mmA');

                }else{

                    return this.moment(date).format('MMM DD YYYY');

                }
            },
            fetchInvoices() {

                if( this.localInvoicesUrl ){

                    //  Hold constant reference to the vue instance
                    const self = this;

                    //  Start loader
                    self.isLoadingInvoices = true;

                    //  Make sure we are not displaying any invoice
                    self.activeInvoiceUrl = null;

                    //  Console log to acknowledge the start of api process
                    console.log('Start getting invoices...');

                    let url = this.localInvoicesUrl + '?';

                    /** Get all the status of the used to filter the invoices
                     *  We combine as the available statues e.g:
                     * 
                     *  General Statuses = Open / Archieved / Cancelled e.t.c
                     * 
                     *  Payment Statuses = Paid / Unpaid / Pending, e.t.c
                     * 
                     *  All the statuses are separated into arrays e.g:
                     * 
                     *  generalStatuses = ['Open']
                     *  selectedPaymentStatuses = ['Paid', 'Unpaid', 'Pending']
                     * 
                     *  We need to join them into one array with values separated by a comma e.g:
                     * 
                     *  let status = ['Open', 'Paid', 'Unpaid', 'Pending']
                     * 
                    */
                    let status = (this.activeInvoiceTab != 'all' ? this.activeInvoiceTab : '');
                    let paymentStatus = this.selectedPaymentStatuses.join();

                    //  Append the filter by status values
                    url += 'status=' + status;
                    url += '&paymentStatus=' + paymentStatus;

                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', url)
                        .then(({data}) => {
                            
                            //  Console log the data returned
                            console.log(data);

                            //  Stop loader
                            self.isLoadingInvoices = false;

                            //  Invoice the invoice data
                            self.localInvoices = ((data || {})._embedded || {}).invoices || [];

                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isLoadingInvoices = false;

                            //  Console log Error Location
                            console.log('widgets/invoice/list/table.vue - Error getting invoices...');

                            //  Log the responce
                            console.log(response);    
                        });
                }

            }
        },
        created(){
            //  Fetch the invoices
            this.fetchInvoices();
        }
    };
  
</script>