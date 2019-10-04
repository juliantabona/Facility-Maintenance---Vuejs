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
        <Row :gutter="20">
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
                                    <span>Send Email/Sms <Icon type="ios-arrow-down" :size="20" /></span>
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
                        <span class="font-weight-bold mr-2">Customer Note: </span>
                                            
                        <Poptip title="Customer says" trigger="hover" 
                                :content="localOrder.customer_note" placement="top" width="300">
                            <Icon type="ios-chatbubbles-outline" :size="20" />
                        </Poptip>

                    </span>
                    <span class="d-block pt-2 mt-2 border-top"><span class="font-weight-bold">Amount Paid: </span>{{ localOrder.transaction_total | currency(currencySymbol) }}</span>
                    <span class="d-block"><span class="font-weight-bold">Amount Due: </span>{{ localOrder.outstanding_balance | currency(currencySymbol) }}</span>
                    <basicButton customClass="w-100 p-2" class="d-inline-block mt-2 mb-2" :style="{ maxWidth: '250px', position:'relative' }"
                        type="info" size="small" :ripple="false"
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

                            <Steps v-if="(localOrder.lifecycle_history).length" 
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

        <viewProofOfPaymentModal
            v-if="isOpenViewProofOfPaymentModal" 
            :docUrl=" localOrder ? '/api/orders/'+localOrder.id+'/documents?type=payment_proof' : null"
            @visibility="isOpenViewProofOfPaymentModal = $event">
        </viewProofOfPaymentModal>
    </div>
</template>

<script>
    
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
            }
        },
        components: { 
            viewProofOfPaymentModal, basicButton, Loader, scrollBox, orderLifecycle, customerSummaryCard, animatedCheckmark
        },
        data(){
            return {
                localOrder: this.order,
                currencySymbol: ((this.order.currency_type || {}).currency || {}).symbol,
                isOpenViewProofOfPaymentModal: false,
                currentStatus: {}
            }
        },
        computed: {
            customerName(){
                return ( (this.localOrder.billing_info || {}).first_name || (this.localOrder.billing_info || {} ).name )
                        +' '+ (this.localOrder.billing_info || {}).last_name;
            },
            customerAddress(){
                return ( (this.localOrder.billing_info || {}).address_1 || (this.localOrder.billing_info || {}).address_2 );
            },
            customerPhoneNumbers(){
                var phoneList = '';
                var phones = (this.localOrder.billing_info || {}).phones || [];
                
                for( var x=0; x < phones.length; x++ ){
                    
                    if( phones[x].type != 'fax' ){

                        if(x != 0){
                            phoneList = phoneList + ', ';
                        }

                        phoneList = phoneList + '(+' + phones[x]['calling_code']['calling_code'] + ') ' + phones[x]['number'];
                        
                    }
                        
                }

                return phoneList;

            },
            numberOfItemsPurchased(){
                return (this.localOrder.line_items).length
            }
        },
        methods: {
            updateStatusPoptip(status){
                this.currentStatus = status;
            },
            updateOrder(order){
                //  Update the order
                this.localOrder = order;
                this.$emit('updated', order)
            },
        },
        created(){

        }
    };
  
</script>