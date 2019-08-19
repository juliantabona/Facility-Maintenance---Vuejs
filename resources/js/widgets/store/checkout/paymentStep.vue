<style scoped>

    .el-form--label-top >>> .el-form-item__label {
        margin: 0;
        padding: 0;
        line-height: 2em;
    }

</style>

<template>
    
    <!--   Payment Details -->
    <Row :gutter="12">

        <!-- White overlay when placing order -->
        <Spin size="large" fix v-if="isSubmittingOrder" style="border-radius: 10px;">
            <!-- Icon to show as loader  -->
            <clockLoader></clockLoader>
        </Spin>

        <Col :span="24" class="mb-5">
            <Alert class="mb-4">
                <span class="font-weight-bold">Select Payment Method</span>
                <template slot="desc">How would yout like to pay? Select any prefered payment method to pay</template>
            </Alert>

            <!--  Card / Mobile / Offline Payment Tabs --> 
            <Tabs type="card" value="card" class="pb-4">

                <!--  Card Payment Tab -->  
                <TabPane label="Credit/Debit Card" name="card">

                    <Row :gutter="20">

                        <Col :span="8">
                            <IconAndCounterCard 
                                title="Credit/Debit Card" 
                                imageSrc="/images/assets/graphics/credit-card.png"
                                :imageStyle="{ width:'100%' }"
                                :innerBoxStyle="{ padding: '0' }"
                                :titleStyle="{ marginTop:'10px', padding:'0' }"
                                type="success"
                                :showCheckMark="true"
                                :checkMarkVisibility="(selectedPaymentMethod == 'card')"
                                @click.native="selectedPaymentMethod = 'card'">
                            </IconAndCounterCard>
                        </Col>

                    </Row>

                </TabPane>

                <!--  Mobile Payment Tab -->  
                <TabPane label="Mobile Payment" name="mobile">

                    <Row :gutter="20">

                        <Col :span="8">
                            <IconAndCounterCard 
                                title="Orange Money" 
                                imageSrc="/images/samples/orange_money_logo.png"
                                :imageStyle="{ width:'65%', margin: '0 auto', display: 'block' }"
                                :innerBoxStyle="{ padding: '0' }"
                                :titleStyle="{ marginTop:'10px', padding:'0' }"
                                type="success"
                                :showCheckMark="true"
                                :checkMarkVisibility="(selectedPaymentMethod == 'orange')"
                                @click.native="selectedPaymentMethod = 'orange'">
                            </IconAndCounterCard>
                        </Col>

                        <Col :span="8">
                            <IconAndCounterCard 
                                title="MyZaka" 
                                imageSrc="/images/samples/myzaka_logo.png"
                                :imageStyle="{ width:'65%', margin: '0 auto', display: 'block' }"
                                :innerBoxStyle="{ padding: '0' }"
                                :titleStyle="{ marginTop:'10px', padding:'0' }"
                                type="success"
                                :showCheckMark="true"
                                :checkMarkVisibility="(selectedPaymentMethod == 'mascom')"
                                @click.native="selectedPaymentMethod = 'mascom'">
                            </IconAndCounterCard>
                        </Col>

                    </Row>

                </TabPane>

                <!--  Offline Payment Tab -->  
                <TabPane label="Offline Payment" name="offline">

                    <Row :gutter="20">

                        <Col :span="8">
                            <IconAndCounterCard 
                                title="Cash Deposit" 
                                imageSrc="/images/assets/graphics/bank-atm-machine.png"
                                :imageStyle="{ width:'60%',display:'block', margin:'auto' }"
                                :innerBoxStyle="{ padding: '0' }"
                                :titleStyle="{ marginTop:'10px', padding:'0' }"
                                type="success"
                                :showCheckMark="true"
                                :checkMarkVisibility="(selectedPaymentMethod == 'cash-deposit')"
                                @click.native="selectedPaymentMethod = 'cash-deposit'">
                            </IconAndCounterCard>
                        </Col>

                        <Col :span="8">
                            <IconAndCounterCard 
                                title="Bank Transfer" 
                                imageSrc="/images/assets/graphics/bank-atm-machine.png"
                                :imageStyle="{ width:'60%',display:'block', margin:'auto' }"
                                :innerBoxStyle="{ padding: '0' }"
                                :titleStyle="{ marginTop:'10px', padding:'0' }"
                                type="success"
                                :showCheckMark="true"
                                :checkMarkVisibility="(selectedPaymentMethod == 'bank-transfer')"
                                @click.native="selectedPaymentMethod = 'bank-transfer'">
                            </IconAndCounterCard>
                        </Col>

                        <Col :span="8">
                            <IconAndCounterCard 
                                title="Cheque" 
                                imageSrc="/images/assets/graphics/cheque.png"
                                :imageStyle="{ width:'100%', margin:' 20px 0 10px 0' }"
                                :innerBoxStyle="{ padding: '0' }"
                                :titleStyle="{ marginTop:'10px', padding:'0' }"
                                type="success"
                                :showCheckMark="true"
                                :checkMarkVisibility="(selectedPaymentMethod == 'cheque')"
                                @click.native="selectedPaymentMethod = 'cheque'">
                            </IconAndCounterCard>
                        </Col>

                    </Row>

                </TabPane>

            </Tabs>

        </Col>

        <Col :span="24" class="clearfix">
            <!-- Continue button -->
            <basicButton
                v-if="selectedPaymentMethod"
                class="float-right mb-2 ml-3" 
                type="success" size="large" 
                :ripple="true"
                @click.native="handleProceedToPayment()">
                <span>Proceed To Payment</span>
                <Icon type="md-arrow-forward" class="ml-1" />
            </basicButton>

            <!-- Back button -->
            <basicButton
                class="float-right mb-2 ml-3" 
                type="default" size="large" 
                :ripple="false"
                @click.native="updateCheckoutProgress(0)">
                <Icon type="md-arrow-back" class="mr-1" />
                <span>Back</span>
            </basicButton>
        </Col>

        <!-- VCS Form -->
        <form ref="vcsform" method="POST" action="https://www.vcs.co.za/vvonline/vcspay.aspx">
            <input type="hidden" name="_token" :value="csrf_token" />
            <input type="hidden" name="p1" value="3344"> 
            <input type="hidden" name="p2" value="1"> 
            <input type="hidden" name="p3" :value="'Customer payment to '"> 
            <input type="hidden" name="p4" value="980"> 
            <input type="hidden" name="UrlsProvided" value="Y">  
            <input type="hidden" name="ApprovedUrl" value="/paymentSuccessful">  
            <input type="hidden" name="DeclinedUrl" value="/paymentUnSuccessful">   
            <input type="hidden" name="p10" value="/paymentUnSuccessful"> 
        </form>

    </Row>

</template>

<script>
    /*  Buttons  */
    import basicButton from './../../../components/_common/buttons/basicButton.vue';

    /*  Cards  */
    import IconAndCounterCard from './../../../components/_common/cards/IconAndCounterCard.vue';

    import clockLoader from './../../../components/_common/loaders/clockLoader.vue';

    export default {
        components: { 
            basicButton, IconAndCounterCard, clockLoader
        },
        props: {
            checkoutProgress: {
                type: Number,
                default: 0
            }
        },
        data(){
            return {
                user: auth.user,
                csrf_token: window.axios.defaults.headers.common['X-CSRF-TOKEN'],
                selectedPaymentMethod: '',

                localCheckoutProgress: this.checkoutProgress,
                isSubmittingOrder: false
            }
        },
        watch: {
            checkoutProgress: {
                handler: function (val, oldVal) {
                    this.localCheckoutProgress = val;
                },
                deep: true
            },
        },
        methods: {
            updateCheckoutProgress(proceed){
                if(proceed){

                    this.$emit('proceed');

                }else{

                    this.$emit('back');
                    
                }
            },
            handleProceedToPayment(){

                var self = this;

                self.isSubmittingOrder= true;

                console.log('Attempt to submit order...');

                //  Form data to send
                let orderData = { 
                        //  General details
                        number: '1005',
                        order_key: 'dm_order_58d2d042d1d',
                        status: 'pending-payment',
                        currency_type: {
                            country: 'Botswana',
                            currency: {
                                iso: {
                                code: 'BWP',
                                number: '072'
                                },
                                name: 'Pula',
                                symbol: 'P'
                            }
                        },
                        //  Item Info
                        line_items: [
                            {
                                id: 36,
                                name: 'Rolex wrist watch',
                                description: 'Stylish x3 series rolex watch',
                                type: 'product',
                                taxes: [],
                                purchasePrice: 1250,
                                unit_price: 1800,
                                total_price: 3600,
                                quantity: '2'
                            }
                        ],

                        //  Shipping Info
                        shipping_lines: null,

                        //  Grand Total, Subtotal, Shipping Total, Discount Total
                        cart_total: 10.00,
                        shipping_total: 0.00,
                        discount_total: 0.00,
                        grand_total: 15.00,

                        //  Tax Info
                        cart_tax: 2.00,
                        shipping_tax: 0.00,
                        discount_tax: 0.00,
                        grand_total_tax: 3.00,
                        prices_include_tax: 0,
                        tax_lines: null,

                        //  Customer Info
                        client_id: 91,
                        client_type: 'company',
                        customer_ip_address: null,
                        customer_user_agent: null,
                        customer_note: 'Deliver before end of this week',
                        
                        billing_info: {
                            first_name: 'Julian',
                            last_name: 'Tabona',
                            address_1: 'Plot 4567, Extension 12',
                            country: 'Botswana',
                            province: 'South-East',
                            city: 'Gaborone',
                            postal_or_zipcode:"PO Box 456 AAH Masa",
                            email: 'brandontabona@gmail.com',
                            additional_email: 'brandontabona@yahoo.com',
                            phones: [
                                {
                                id: 164,
                                type: 'tel',
                                calling_code: {
                                    country: 'Botswana',
                                    calling_code: '267',
                                    flag: '<span class="flag-icon flag-icon-bw"></span>'
                                },
                                number: 3990960,
                                provider: null,
                                trackable_id: 91,
                                trackable_type: 'company',
                                created_by: 55,
                                company_branch_id: 46,
                                company_id: 49,
                                created_at: '2019-06-18 17:22:03',
                                updated_at: '2019-06-18 17:22:03',
                                }
                            ]
                            },
                            shipping_info: {
                                first_name: 'Bonolo',
                                last_name: 'Sesiane',
                                address_1: 'Plot 6721, Block 8',
                                country: 'Botswana',
                                province: 'South-East',
                                city: 'Gaborone',
                                email: 'bonolosesiane@gmail.com',
                                additional_email: 'bonolosesiane@yahoo.com',
                                postal_or_zipcode:"PO Box 623 CAA Masa"
                            },

                        //  Payment Info
                        payment_method: 'bank_deposit',
                        payment_method_title: 'Bank Deposit',
                        transaction_id: null,
                        date_paid: null,

                        //  Store, Company & Branch Info
                        store_id: 1,

                            mail: {
                                primaryEmails: ['brandontabona@gmail.com'],
                                ccEmails: [],
                                bccEmails: []
                                //subject: this.locaSubject,
                                //message: this.locaMessage
                            },
                            deliveryMethods: ['Email']

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