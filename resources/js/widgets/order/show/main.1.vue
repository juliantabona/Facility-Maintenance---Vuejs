<style scoped>

    .column-equal-height {
        display: flex;
    }

    .full-height{
        height: 100%;
    }

</style>

<template>
    <div>
        <!-- Full Page Layout -->

        <!-- Basic Layout -->
        <Row :gutter="12">

            <!-- Saving Spinner  -->
            <Spin v-if="isSavingOrder" size="large" fix></Spin>

            <Col :span="24">

                <!--    Order Lifecycle   -->
                <Row v-if="localOrder.allow_lifecycle" :gutter="20">
                    <Col :span="24" class="mb-2">
                        <Card :style="{ width: '100%' }">
                                        
                            <Row :gutter="20">
                                <Col :span="18">
                                    <!-- Order Lifecycle -->
                                    <orderLifecycle 
                                        :model="localOrder" 
                                        :modelId="localOrder.id" 
                                        :modelType="localOrder.model_type"
                                        resourceName="Order" 
                                        @updated:lifecycle="updateOrder($event)">
                                    </orderLifecycle>
                                </Col>
                                <Col :span="6">

                                    <Divider type="vertical" />

                                    <!-- Send Email Dropdown Options -->
                                    <Dropdown>
                                        <Button type="default">
                                            <Icon type="ios-mail-outline" :size="20" />
                                            <span>Email / Sms <Icon type="ios-arrow-down" :size="20" /></span>
                                        </Button>
                                        <DropdownMenu slot="list">
                                            <DropdownItem>Order Invoice</DropdownItem>
                                            <DropdownItem>Order Payment Receipt</DropdownItem>
                                            <DropdownItem>Bank Transfer Details</DropdownItem>
                                        </DropdownMenu>
                                    </Dropdown>
                                </Col>
                            </Row>

                        </Card>
                    </Col>
                </Row>

                <!--    Order Status   -->
                <Row v-else :gutter="20">
                    <Col :span="24" class="mb-2">
                        <Card :style="{ width: '100%' }">
                            <Row :gutter="20">

                                <!-- Select Order Status -->
                                <Col :span="8">
                            
                                    <span>Status: </span>
                                    <Poptip trigger="hover" width="400" placement="top-start" word-wrap 
                                            :content="manualStatusHint">

                                        <Select v-model="localOrder.manual_status.name" placeholder="Select order status"
                                                @mouseenter.native="updateManualStatusPoptip(localOrder.manual_status.name)"
                                                style="width: 180px !important;">

                                            <OptionGroup label="Payment status">
                                                <Option v-for="item in ['Pending Payment', 'Failed Payment', 'Paid']" 
                                                        :value="item" :key="item" @mouseover.native="updateManualStatusPoptip(item)">
                                                    {{ item }}
                                                </Option>
                                            </OptionGroup>

                                            <OptionGroup label="Action status">
                                                <Option v-for="item in ['Processing', 'On Hold']" 
                                                        :value="item" :key="item" @mouseover.native="updateManualStatusPoptip(item)">
                                                    {{ item }}
                                                </Option>
                                            </OptionGroup>

                                            <OptionGroup label="Final status">
                                                <Option v-for="item in ['Cancelled', 'Completed']" 
                                                        :value="item" :key="item" @mouseover.native="updateManualStatusPoptip(item)">
                                                    {{ item }}
                                                </Option>
                                            </OptionGroup>

                                        </Select>
                                    </Poptip>
                                </Col>

                                <!-- Send Email / SMS -->
                                <Col :span="12">

                                    <Divider type="vertical" />

                                    <!-- Send Email / SMS Dropdown -->
                                    <Dropdown>

                                        <Button type="default">
                                            <Icon type="ios-mail-outline" :size="20" />
                                            <span>Send Email / Sms <Icon type="ios-arrow-down" :size="20" /></span>
                                        </Button>

                                        
                                        <!-- Send Email Dropdown Options -->
                                        <DropdownMenu slot="list">
                                            <DropdownItem>Order Invoice</DropdownItem>
                                            <DropdownItem>Order Payment Receipt</DropdownItem>
                                            <DropdownItem>Bank Transfer Details</DropdownItem>
                                        </DropdownMenu>

                                    </Dropdown>

                                </Col>

                                <Col :span="4" class="clearfix">

                                    <!-- Save Button -->
                                    <basicButton class="float-right" type="success" size="large" 
                                                 @click.native="saveOrder()">
                                        <span>Save Changes</span>
                                    </basicButton>

                                </Col>
                            </Row>

                        </Card>
                    </Col>
                </Row>

                <!--    Customer / Order / Activity details   -->
                <Row :gutter="20" class="column-equal-height">

                    <Col :span="8">
                        <!--    Show the customers details   -->
                        <customerSummaryCard :customer="order.billing_info"></customerSummaryCard>
                    </Col>

                    <Col :span="8">
                        <Card class="full-height">

                            <Poptip 
                                v-if="(localOrder.current_lifecycle_main_status || {}).title == 'Completed'" 
                                trigger="hover" content="Order has been completed" placement="top" width="300"
                                :style="{ position: 'absolute', right: '10px', top: '10px' }">
                                <!-- Animated checkmark  -->
                                <animatedCheckmark :style="{ width: '30px', height: 'auto' }"></animatedCheckmark>
                            </Poptip>

                            <h4 class="text-primary mb-3">Order</h4>
                            <span class="d-block"><span class="font-weight-bold">Total Cost: </span> {{ localOrder.grand_total | currency(currencySymbol) }}</span>
                            <span class="d-block"><span class="font-weight-bold">Purchased: </span>{{ numberOfItemsPurchased + (numberOfItemsPurchased == 1 ? ' Item' : ' Items') }}</span>
                            <span class="d-block">
                                <span class="font-weight-bold">Customer Note: </span>

                                <span v-if="localOrder.customer_note" class="ml-2">                  
                                    <Poptip title="Customer says" trigger="hover" 
                                            :content="localOrder.customer_note" placement="top" width="300">
                                        <Icon type="ios-chatbubbles-outline" :size="20" />
                                    </Poptip>
                                </span>

                                <span v-else>N/A</span>

                            </span>
                            <span class="d-block pt-2 mt-2 border-top"><span class="font-weight-bold">Amount Paid: </span>{{ localOrder.transaction_total | currency(currencySymbol) }}</span>
                            <span class="d-block"><span class="font-weight-bold">Amount Due: </span>{{ localOrder.outstanding_balance | currency(currencySymbol) }}</span>
                            <basicButton 
                                class="mt-2 mb-2"
                                type="info" size="default" :ripple="false"
                                @click.native="isOpenViewProofOfPaymentModal = true">
                                <Icon type="ios-paper" :size="20" class="mr-1" style="margin-top: -4px;" />
                                <span>View Proof Of Payment</span>
                            </basicButton>

                            <span class="d-block pt-2 mt-2 border-top">
                                <span class="font-weight-bold mr-2">Refunds: </span>
                                <span>{{ localOrder.refund_total | currency(currencySymbol) }}</span>

                                <Poptip v-if="(localOrder.refunds || {}).length" trigger="hover" placement="top" width="300">
                                    <Icon type="ios-information-circle-outline" :size="20" class="ml-2" />
                                    <template slot="content">
                                        <div v-for="(refund, i) in localOrder.refunds" :key="i"
                                            style=" width: 100%; white-space: normal; "
                                            :class="'mb-2' + (i < (localOrder.refunds.length - 1) ? ' border-bottom': '')">
                                            {{ refund.reason }}
                                        </div>
                                    </template>
                                </Poptip>

                            </span>
                        </Card>
                    </Col>

                    <Col :span="8">

                        <Card class="full-height">

                            <h4 class="text-primary mb-3">Status</h4>
                            
                            <Poptip placement="left-start" width="300" trigger="hover">
                                <div slot="content" style="white-space: normal;">

                                    <h6 class="border-bottom font-weight-bold mb-2 pb-2 pt-2">{{ currentStatus.title }}</h6>

                                    <span class="d-block mt-2 mb-2">
                                        <span class="font-weight-bold">
                                            {{ currentStatus.title == 'Open' ? 'Submitted By: ' : 
                                                ( currentStatus.title == 'Completed' ? 'Completed By: ' : 'Updated By: ' ) }} 
                                        </span>
                                        <span>{{ ((currentStatus || {})['created_by'] || [])['full_name'] }}</span>
                                    </span>

                                    <span v-if="(currentStatus || {})['reason']" class="d-block mb-2">
                                        <span class="font-weight-bold">Reason:</span>
                                        <span>{{ (currentStatus || {})['reason'] }}</span>
                                    </span>

                                </div>
                                <scrollBox class="border">

                                    <Steps v-if="(localOrder.lifecycle_history || {}).length" 
                                        direction="vertical" class="pt-2 pl-2 pr-3 pb-2" style="max-height: 190px;"
                                        :current="((localOrder.lifecycle_history).length || 0) - 1">
                                        
                                        <Step v-for="(status, i) in localOrder.lifecycle_history" :key="i"
                                            :title="status.title" :content="status.description" 
                                            @mouseover.native="updateStatusPoptip(status)">
                                        </Step>
                                    </Steps>

                                    <Alert v-else type="warning" show-icon>No activities found</Alert>

                                </scrollBox>
                            </Poptip>

                        </Card>
                    </Col>
                </Row>

            </Col>

            <!-- Order Items & Cost -->
            <Col v-if="orderSettings" :span="24" class="mt-2 mb-2">

                <!-- Order Items List -->
                <Card :style="{ width: '100%' }">
            
                    <Row :gutter="20">

                        <!-- Order Items List -->
                        <Col :span="24">

                            <items :order="localOrder" :settings="orderSettings" :editMode="false"></items>

                        </Col>

                        <Divider dashed class="mt-0 mb-4" />

                        <!-- Cost breakdown details e.g) Sub / Grand total and tax amounts -->
                        <Col :span="24">

                            <Row>

                                <Col span="12" offset="12" class="pr-4">
                                    
                                    <totalBreakDown :order="localOrder" :settings="orderSettings" :editMode="false"></totalBreakDown>

                                </Col>

                            </Row>

                        </Col>

                    </Row>

                </Card>
                
            </Col>

        </Row>

        <viewProofOfPaymentModal
            v-if="isOpenViewProofOfPaymentModal" 
            :docUrl=" localOrder ? '/api/orders/'+localOrder.id+'/documents?type=payment_proof' : null"
            @visibility="isOpenViewProofOfPaymentModal = $event">
        </viewProofOfPaymentModal>

    </div>
</template>

<script>
    
    /*  Local components    */
    import items from './items.vue'
    import totalBreakDown from './totalBreakDown.vue';

    /*  Modals  */
    import viewProofOfPaymentModal from './../../../components/_common/modals/viewProofOfPaymentModal.vue';

    /*  Buttons  */
    import basicButton from './../../../components/_common/buttons/basicButton.vue';

    /*  Loaders  */
    import Loader from './../../../components/_common/loaders/Loader.vue';  

    /*  scrollBox  */
    import scrollBox from './../../../components/_common/scrollBox/scrollBox.vue';

    /*  Lifecycles  */
    import orderLifecycle from './../../../components/_common/lifecycles/orderLifecycle.vue';

    /*  Cards */
    import customerSummaryCard from './../../../components/_common/cards/customerSummaryCard.vue';

    /*  Animated Icons */
    import animatedCheckmark from './../../../components/_common/animatedIcons/animatedCheckmark.vue';

    export default {
        props:{
            order: {
                type: Object,
                default: null
            },
            store: {
                type: Object,
                default: null
            }
        },
        components: { 
            items, totalBreakDown, viewProofOfPaymentModal, basicButton, Loader, scrollBox, orderLifecycle, customerSummaryCard, animatedCheckmark
        },
        data(){
            return {
                orderSettings: null,
                isSavingOrder: false,
                localOrder: this.order,
                manualStatusHint: null,
                currencySymbol: ((this.order || {}).currency || {}).symbol,
                isOpenViewProofOfPaymentModal: false,
                currentStatus: {}
            }
        },
        computed: {
            numberOfItemsPurchased(){
                return (this.localOrder.item_lines || {}).length || 0;
            }
        },
        methods: {
            updateManualStatusPoptip(status){

                var status_description = '';

                switch(status) {
                    case 'Pending Payment':
                        status_description = 'The order has not been paid (unpaid)';
                        break;
                    case 'Failed Payment':
                        status_description = 'The order payment failed or was declined (unpaid).';
                        break;
                    case 'Paid':
                        status_description = 'The order has been paid';
                        break;
                    case 'Processing':
                        status_description = 'Payment received (paid) and stock has been reduced. The order is now awaiting fulfillment.';
                        break;
                    case 'On Hold':
                        status_description = 'Awaiting payment – stock is reduced, but payment requires confirmation.';
                        break;
                    case 'Cancelled':
                        status_description = 'Order fulfilled and complete – requires no further action';
                        break;
                    case 'Completed':
                        status_description = 'Order fulfilled and complete – requires no further action';
                        break;
                    default:
                        status_description = 'Status is unknown.';
                }

                this.manualStatusHint = status_description;

            },
            updateStatusPoptip(status){
                this.currentStatus = status;
            },
            updateOrder(order){
                //  Update the order
                this.localOrder = order;
                this.$emit('updated', order)
            },
            fetchStoreSettings() {

                //  If we have the link to get the store settings
                if( this.store._links['oq:settings'].href ){

                    //  Hold constant reference to the vue instance
                    const self = this;

                    //  Start loader
                    self.isLoadingSettings = true;

                    //  Console log to acknowledge the start of api process
                    console.log('Start getting store settings...');

                    //  Use the api call() function located in resources/js/api.js
                    api.call('get', this.store._links['oq:settings'].href)
                        .then(({data}) => {
                            
                            //  Console log the data returned
                            console.log(data);

                            //  Stop loader
                            self.isLoadingSettings = false;

                            //  Order the order data
                            self.orderSettings = ((data || {}).details || {}).orderTemplate;

                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isLoadingSettings = false;

                            //  Console log Error Location
                            console.log('dashboard/order/show/main.vue - Error getting store settings...');

                            //  Log the responce
                            console.log(response);    
                        });
                }

            },
            saveOrder() {

                //  If we have the order POST link
                if( this.localOrder._links.self.href ){

                    //  Hold constant reference to the vue instance
                    const self = this;

                    //  Start loader
                    self.isSavingOrder = true;

                    var orderData = {
                        manual_status: this.localOrder.manual_status.name
                    }

                    //  Console log to acknowledge the start of api process
                    console.log('Start saving the order...');

                    //  Use the api call() function located in resources/js/api.js
                    api.call('post', this.localOrder._links.self.href, orderData)
                        .then(({data}) => {
                            
                            //  Console log the data returned
                            console.log(data);

                            //  Stop loader
                            self.isSavingOrder = false;

                            //  Order the order data
                            self.localOrder = data;

                            self.$emit('updated', data);

                        })         
                        .catch(response => { 

                            //  Stop loader
                            self.isSavingOrder = false;

                            //  Console log Error Location
                            console.log('dashboard/order/show/main.vue - Error saving the order...');

                            //  Log the responce
                            console.log(response);    
                        });
                }

            }
        },
        created(){

            //  Get the store settings  
            this.fetchStoreSettings();

        }
    };
  
</script>