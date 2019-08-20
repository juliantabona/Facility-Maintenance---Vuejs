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
                @click.native="$emit('proceedToPayment')">
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
            selectedPaymentMethod: {
                handler: function (val, oldVal) {
                    this.$emit('updated:paymentMethod', selectedPaymentMethod)
                },
                deep: true
            },
            shippingInfo: {
                handler: function (val, oldVal) {
                    this.localShippingInfo = val;
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
            }
        },
        created(){
            
        }
    };
  
</script>