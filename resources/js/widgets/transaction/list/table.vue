<style scoped>

    .transaction-card >>> .ivu-card-head{
        padding-bottom:0 !important;
    }

    .transaction-card >>> .transaction-menu {
        z-index: 1;
        height: 35px;
        line-height: 30px;
        background: transparent;
    }

    .transaction-card >>> .transaction-menu:after {
        background: transparent;
    }

    .transaction-card >>> .transaction-menu li {
        padding: 0 15px;
        font-size: 12px !important;
    }

    .transaction-search-bar >>> input {
        height: 30px;
        border: none;
    }

    .transaction-filter-dropdown >>> .ivu-select-dropdown {
        top: 30px !important;
        left: 0px !important;
    }

    .transaction-columns-dropdown >>> .ivu-select-dropdown {
        top: 30px !important;
        right: 32px !important;
        left: inherit !important;
    }

    .filter-tag-box{
        padding: 10px 5px 0 5px;
        border-top: 3px solid #e4b52e;
    }

    .transaction-table >>> .ivu-spin-fix {
        z-index: 1 !important;
    }

    .transaction-table >>> .breakdown-poptip .ivu-poptip-body{
        padding: 0 16px;
    }

    .transaction-table >>> .breakdown-poptip .ivu-list-container{
        margin-top: -20px !important;
        margin-bottom: -20px !important;
    }

    .transaction-table, .transaction-table >>> .ivu-table{
        overflow: inherit !important;
    }

    .transaction-table >>> thead tr th span{
        font-weight: bold;
    }

    .transaction-table >>> .ivu-checkbox-wrapper{
        margin: 0;
        padding: 0;
    }

    .transaction-table >>> .cancelled{
        text-decoration: line-through;
    }

</style>

<template>

    <div>

        <!-- If we have an active Transaction to display -->
        <template v-if="activeTransactionUrl">

            <!-- Show active Transaction widget -->
            <showActiveTransactionWidget :TransactionUrl="activeTransactionUrl" :transactions="localTransactions"
                @changeTransaction="activeTransactionUrl = $event" 
                @goBack="fetchTransactions()">
            </showActiveTransactionWidget>

        </template>

        <!-- If we don't have an active Transaction to display -->
        <template v-else>

            <Card dis-hover class="transaction-card p-2">

                <!-- (1) Transaction Status [ All / Open / Archieved / Cancelled ] 
                    (2) Refresh Transactions Button 
                -->
                <Row slot="title" class="mb-2">

                    <Col :span="24">
                        
                        <!-- Custom Heading / Refresh Transactions Button -->
                        <div class="clearfix">

                            <!-- Custom Heading -->
                            <h5 v-if="title" class="d-block float-left font-weight-bold">
                                {{ title }}
                            </h5>

                            <!-- Refresh Transactions Button -->
                            <basicButton @click.native="fetchTransactions()" 
                                        type="success" class="float-right"
                                        :disabled="isLoadingTransactions">
                                        <Icon type="ios-refresh" :size="20"/>
                                        <span>Refresh</span>
                            </basicButton>
                        </div>
                    </Col>

                </Row>

                <div v-if="showFilterAndSortingOptions" class="mb-4">
                
                    <!-- Transaction Search / Filter By Payment Status / Sort -->
                    <Row>

                        <Col :span="16">

                            <ButtonGroup>

                                <!-- Search Field -->
                                <Button class="p-0">
                                    <Input v-model="searchedTransaction" prefix="ios-search" placeholder="Search transactions..." 
                                        clearable class="transaction-search-bar" style="width: 200px;" />
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

                                                    <DropdownItem>Transaction number (ascending)</DropdownItem>
                                                    <DropdownItem>Transaction number (descending)</DropdownItem>
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
                                                            <Checkbox class="d-block" label="Transaction Selector"></Checkbox>
                                                            <Checkbox class="d-block" label="Show Summary Arrow"></Checkbox>
                                                            <Checkbox class="d-block" label="Payment Indicator"></Checkbox>
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

                <!-- Selected Transaction(s) Actions / Transaction List -->
                <div>

                    <!-- Selected Transaction(s) Actions -->
                    <Row v-if="selectedTransactions.length" class="mb-2">

                        <Col :span="16">

                            <ButtonGroup>

                                <!-- Number Of Selected Transactions -->
                                <Button class="pr-4 pl-4">
                                    <Badge :count="selectedTransactions.length" type="warning" class="mr-1"></Badge> 
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

                                            <DropdownItem>Archive transactions</DropdownItem>
                                            <DropdownItem>Unarchive transactions</DropdownItem>

                                            <Divider class="mt-1 mb-1"></Divider>

                                            <DropdownItem>Add tags</DropdownItem>
                                            <DropdownItem>Remove tags</DropdownItem>

                                        </DropdownMenu>

                                    </Dropdown>

                                </Button>
                            
                            </ButtonGroup>

                        </Col>

                    </Row>

                    <!-- Store Transactions -->
                    <Table :columns="dynamicColumns" :data="localTransactions"
                        no-data-text="No transactions found" :loading="isLoadingTransactions"
                        @on-select-all-cancel="manageSelectedAllTransactions"
                        @on-select-all="manageSelectedAllTransactions"
                        @on-select-cancel="manageSelectedTransaction"
                        @on-select="manageSelectedTransaction"
                        class="transaction-table">
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
    import transactionStatusBadge from './../../../components/_common/statuses/transactionStatusBadge.vue';   
    
    /*  Active Transaction Widget  */
    import showActiveTransactionWidget from './../show/main.vue';

    /* Expand Table Row  */
    import transactionRowDropDown from './../show/main.vue';  

    import moment from 'moment';

    export default {
        props: {
            transactionsUrl: {
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
            basicButton, Loader, transactionStatusBadge, showActiveTransactionWidget, transactionRowDropDown
        },
        data(){
            return {
                moment: moment,
                //  Current store transactions
                localTransactions: [],
                //  Selected transactions
                selectedTransactions: [],
                //  Active transaction url
                activeTransactionUrl: null,
                //  Searched transaction value
                searchedTransaction: null,
                //  LLoading transactions status
                isLoadingTransactions: false,
                //  Get the url used to fetch the store transactions
                localTransactionsUrl: this.transactionsUrl,
                //  Check if we have any payment filters stored on the url query
                selectedPaymentStatuses: this.getSelectedPaymentStatuses(),
                //  Set the default columns to show
                tableColumnsToShowByDefault:[
                    'Transaction #', 'Invoice #', 'Type', 'Status',
                    'Payment Type', 'Amount', 'Date'
                ],
            }
        },
        watch: {
            /** Watch for changes on the transactions url. If this value changes we need to 
             *  refetch all the transactions
             */  
            transactionsUrl: {
                handler: function (val, oldVal) {

                    //  Get the updated transactions url
                    this.localTransactionsUrl = val;

                    //  Refetch the transactions
                    this.fetchTransactions();

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
            dynamicColumns(status){ 
                
                var allowedColumns = [];
                
                //  Transaction #
                if(this.tableColumnsToShowByDefault.includes('Transaction #')){
                    allowedColumns.push(
                    {
                        title: 'Transaction #',
                        sortable: true,
                        width:150,
                        render: (h, params) => {
                            return h('span', {
                                class: [(params.row.status.name == 'Cancelled' ? 'text-danger' : '')]
                            }, [
                                h('Button', {
                                    props: {
                                        type: 'text',
                                        ghost: true
                                    },
                                    class: ['text-dark']
                                }, (params.row.id) || '...')
                            ]);
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
                                class: [(params.row.status.name == 'Cancelled' ? 'text-danger' : '')]
                            }, [
                                h('Button', {
                                    props: {
                                        type: 'text',
                                        ghost: true
                                    },
                                    class: ['text-dark']
                                }, params.row.invoice_id )
                            ]);
                        }
                    });
                }
                
                //  Type Details
                if(this.tableColumnsToShowByDefault.includes('Type')){
                    allowedColumns.push(
                    {
                        title: 'Type',
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
                                    content: 'Type: '+(params.row.type  || '...')
                                }
                            }, [
                                h('span', {
                                    class: ['cut-text', 'text-capitalize', (params.row.status.name == 'Cancelled' ? 'text-danger' : '')]
                                }, params.row.type || '...')
                            ])
                        }
                    });
                }
                
                //  Status Details
                if(this.tableColumnsToShowByDefault.includes('Status')){
                    allowedColumns.push(
                    {
                        width: 180,
                        title: 'Status',
                        render: (h, params) => {
                            //  Status Badge
                            return h(transactionStatusBadge, {
                                props: {
                                    status: params.row.status
                                }
                            })
                        }
                    })
                }
                
                //  Automatic Details
                if(this.tableColumnsToShowByDefault.includes('Automatic')){
                    allowedColumns.push(
                    {
                        title: 'Automatic',
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
                                    title: 'Automatic: '+(params.row.automatic ? 'Yes' : 'No'),
                                    content: params.row.automatic 
                                             ? 'This means that the transaction was recorded automatically by the system and not by a person'
                                             : 'This means that the transaction was recorded manually by a person.'
                                }
                            }, [
                                h('span', {
                                    class: ['cut-text', 'text-capitalize', (params.row.status.name == 'Cancelled' ? 'text-danger' : '')]
                                }, params.row.automatic ? 'Yes' : 'No')
                            ])
                        }
                    });
                }
                
                //  Payment Type
                if(this.tableColumnsToShowByDefault.includes('Payment Type')){
                    allowedColumns.push(
                    {
                        title: 'Method',
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
                                    content: 'Payment Method: '+(params.row.payment_type  || '...')
                                }
                            }, [
                                h('span', {
                                    class: ['cut-text', 'text-capitalize', (params.row.status.name == 'Cancelled' ? 'text-danger' : '')]
                                }, params.row.payment_type || '...')
                            ])
                        }
                    });
                }
                
                //  Amount
                if(this.tableColumnsToShowByDefault.includes('Amount')){
                    allowedColumns.push(
                    {
                        width: 150,
                        title: 'Amount',
                        sortable: true,
                        render: (h, params) => {
                            
                            var paymentAmount = (params.row.payment_amount || 0);
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
                                }, this.formatPrice(paymentAmount, symbol) ),
                                h('List', {
                                        slot: 'content',
                                        props: {
                                            slot: 'content',
                                            size: 'small'
                                        }
                                    }, [
                                        h('ListItem', {
                                            class: ['font-weight-bold']
                                        }, 'Amount: '+this.formatPrice(paymentAmount, symbol) )
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

                //  Re-fetch the transactions
                this.fetchTransactions();
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
            removePaymentStatusTag(index){

                //  Remove the payment status
                this.selectedPaymentStatuses.splice(index, 1);

            },
            manageSelectedAllTransactions(selection){
                this.selectedTransactions = selection;
            },
            manageSelectedTransaction(selection, row){
                this.selectedTransactions = selection;
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
            fetchTransactions() {

                if( this.localTransactionsUrl ){

                    //  Hold constant reference to the vue instance
                    const self = this;

                    //  Start loader
                    self.isLoadingTransactions = true;

                    //  Make sure we are not displaying any transaction
                    self.activeTransactionUrl = null;

                    //  Console log to acknowledge the start of api process
                    console.log('Start getting transactions...');

                    let url = this.localTransactionsUrl + '?';

                    /** Get all the status of the used to filter the transactions
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
                    let paymentStatus = this.selectedPaymentStatuses.join();

                    //  Append the filter by status values
                    url += 'paymentStatus=' + paymentStatus;

                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', url)
                        .then(({data}) => {
                            
                            //  Console log the data returned
                            console.log(data);

                            //  Stop loader
                            self.isLoadingTransactions = false;

                            //  Transaction the transaction data
                            self.localTransactions = ((data || {})._embedded || {}).transactions || [];

                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isLoadingTransactions = false;

                            //  Console log Error Location
                            console.log('widgets/transaction/list/table.vue - Error getting transactions...');

                            //  Log the responce
                            console.log(response);    
                        });
                }

            }
        },
        created(){
            //  Fetch the transactions
            this.fetchTransactions();
        }
    };
  
</script>