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
                <Card class="full-height">
                    <h4 class="text-primary mb-3">Customer</h4>
                    <span class="d-block pb-2 mb-2 border-bottom"><span class="font-weight-bold"><Icon type="ios-contact-outline" :size="20"/></span> Julian Tabona</span>
                    <span class="d-block"><span class="font-weight-bold"><Icon type="ios-call-outline" :size="20"/></span> (+267) 75993221</span>
                    <span class="d-block"><span class="font-weight-bold"><Icon type="ios-mail-outline" :size="20" /></span> brandontabona@gmail.com</span>
                    
                    <span class="d-flex pt-2 mt-2 border-top">
                        <Icon type="ios-pin-outline" :size="20" class="mr-1" />
                        <span> Plot 3465, Cleaveland Street, Botswana, Gaborone</span> 
                    </span>
                </Card>
            </Col>

            <Col :span="8">
                <Card class="full-height">
                    <h4 class="text-primary mb-3">Order</h4>
                    <span class="d-block"><span class="font-weight-bold">Total Cost: </span> P4,500.00</span>
                    <span class="d-block"><span class="font-weight-bold">Purchased: </span>3 items</span>
                    <span class="d-block pt-2 mt-2 border-top"><span class="font-weight-bold">Amount Paid: </span>P4,500.00</span>
                    <span class="d-block"><span class="font-weight-bold">Amount Due: </span>P0.00</span>
                    <basicButton customClass="w-100 p-2" class="d-inline-block mt-2 mb-2" :style="{ maxWidth: '250px', position:'relative' }"
                        type="info" size="small" :ripple="false"
                        @click.native="isOpenViewProofOfPaymentModal = true">
                        <Icon type="ios-paper" :size="20" class="mr-1" style="margin-top: -4px;" />
                        <span>View Proof Of Payment</span>
                    </basicButton>

                    <span class="d-block pt-2 mt-2 border-top"><span class="font-weight-bold">Refunds: </span>P500.00</span>
                </Card>
            </Col>

            <Col :span="8">

                <Card class="full-height">
                    <h4 class="text-primary mb-3">Status</h4>
                    <scrollBox class="border">
                        <Steps :current="2" direction="vertical" class="p-2" style="max-height: 190px;">
                            <Step title="Open" content="Received on Jun 12 2018"></Step>
                            <Step title="Paid" content="Paid on Jun 14 2018"></Step>
                            <Step title="Pending Delivery" content="Currently waiting delivery"></Step>
                            <Step title="Completed" content="Completed on Jun 16 2018"></Step>
                        </Steps>
                    </scrollBox>
                </Card>
            </Col>
        </Row>

        <viewProofOfPaymentModal
            v-if="isOpenViewProofOfPaymentModal" 
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

    export default {
        props:{
            order: {
                type: Object,
                default: null
            }
        },
        components: { 
            viewProofOfPaymentModal, basicButton, Loader, scrollBox, orderLifecycle
        },
        data(){
            return {
                localOrder: this.order,
                isOpenViewProofOfPaymentModal: false
            }
        },
        methods: {

        },
        created(){

        }
    };
  
</script>