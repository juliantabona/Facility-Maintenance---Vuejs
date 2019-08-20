<template>
    
    <!-- Checkout Widget -->
    <Row :gutter="20">
        <Col :span="24">
            
            <Row :gutter="12">
                
                <Col :xs="24" :sm="18" :md="18" :lg="18" class="mb-2">

                    <!-- Checkout Heading -->
                    <h1 class="text-center pt-3 pb-4">CHECKOUT</h1>
                    
                    <!-- Checkout Steps Explained -->
                    <Card class="ml-1 mr-1">
                        <Steps :current="checkoutProgress">
                            <Step title="Account" content="Complete basic account details"></Step>
                            <Step title="Delivery" content="Add location details for deliveries"></Step>
                            <Step title="Payment" content="Pay for goods"></Step>
                        </Steps>
                    </Card>

                    <Carousel v-model="checkoutProgress" dots="none" arrow="never" class="pb-5 mt-2 mb-2">
                        
                        <!--   Account Details -->

                        <CarouselItem>
                            <Card class="ml-1 mr-1">   
                                <accountStep
                                    :checkoutProgress="checkoutProgress"
                                    @updated:billingInfo="localBillingInfo = $event"
                                    @proceed="checkoutProgress = checkoutProgress + 1">
                                </accountStep>
                            </Card>
                        </CarouselItem>

                        <!--   Delivery Details -->
                        <CarouselItem>
                            <Card class="ml-1 mr-1">   
                                <deliveryStep  
                                    class="pt-4 pb-4 pr-2 pl-2 mr-1 ml-1" 
                                    :style="{ background: '#f5f7f9', borderRadius:'10px' }"
                                    :checkoutProgress="checkoutProgress"
                                    @updated:shippingInfo="localShippingInfo = $event"
                                    @proceed="checkoutProgress = checkoutProgress + 1"
                                    @back="checkoutProgress = checkoutProgress - 1">
                                </deliveryStep>
                            </Card>
                        </CarouselItem>

                        <!--   Payment Details -->
                        <CarouselItem>
                            <Card class="ml-1 mr-1">   
                                <paymentStep  
                                    :checkoutProgress="checkoutProgress"
                                    @updated:paymentMethod="selectedPaymentMethod = $event"
                                    @proceed="checkoutProgress = checkoutProgress + 1"
                                    @back="checkoutProgress = checkoutProgress - 1"
                                    @proceedToPayment="handleProceedToPayment()">
                                </paymentStep>
                            </Card>
                        </CarouselItem>

                    </Carousel>

                    <div class="tt-shopcart-btn">
                        <div class="col-left">
                            <span class="btn btn-link"><i class="icon-e-19"></i>CONTINUE SHOPPING</span>
                        </div>
                    </div>
                </Col>
                <Col :xs="24" :sm="6" :md="6" :lg="6" class="mb-2">
                    
                    <cartWidget cartType="widget-cart" :hideCheckoutBtn="true"></cartWidget>

                </Col>
            </Row>
        </Col>
    </Row>

</template>

<script>

    /*  Cart Widget  */
    import cartWidget from './../cart/main.vue';

    /*  Checkout Steps  */
    import accountStep from './accountStep.vue';
    import deliveryStep from './deliveryStep.vue';
    import paymentStep from './paymentStep.vue';

    export default {
        components: { 
            cartWidget, accountStep, deliveryStep, paymentStep
        },
        props: {
            products: {
                type: Array,
                default: function(){
                    return [];
                }
            }
        },
        data(){
            return {
                user: auth.user,
                localProducts: this.products,
                checkoutProgress: 0,

                selectedPaymentMethod: null,
                localBillingInfo: this.billingInfo,
                localShippingInfo: this.shippingInfo,
                customerNote: '',

            }
        },
        watch: {
            products: {
                handler: function (val, oldVal) {
                    this.localProducts = val;
                },
                deep: true
            }
        },
        methods: {
            handleProceedToPayment(){

                var self = this;

                self.isSubmittingOrder= true;

                console.log('Attempt to submit order...');

                //  Form data to send
                let orderData = { 

                        //  Customer Info
                        submitted_by: user.id,
                        billing_info: this.localBillingInfo,
                        shipping_info: this.localShippingInfo,
                        customer_note: this.customerNote,
                        
                        //  Payment Info
                        payment_method: this.selectedPaymentMethod,

                        //  Store, Company & Branch Info
                        store_id: this.store.id,

                        //  Mail/Sms delivery info
                        deliveryMethods: ['Email'],
                        mail: {
                            primaryEmails: [ user.email ],
                            ccEmails: [],
                            bccEmails: []
                        }

                 };

                console.log(orderData);
                
                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/orders', orderData)
                    .then(({ data }) => {

                        //  Alert creation success
                        self.$Message.success('Order saved sucessfully!');

                        self.isSubmittingOrder= false;

                        //  If the payment method is Credit/Debit Card
                        if( self.selectedPaymentMethod == 'card' ){
                                            
                            //  Submit the VCS Form
                            //this.$refs.vcsform.submit();

                        }

                    })         
                    .catch(response => { 
                        
                        self.isSubmittingOrder= false;

                        console.log('productSummaryWidget.vue - Error saving product...');
                        console.log(response);
                    });
            },
        },
        created(){
            
        }
    };
  
</script>