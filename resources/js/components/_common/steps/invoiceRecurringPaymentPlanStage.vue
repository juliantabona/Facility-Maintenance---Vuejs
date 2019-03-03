<style scoped>

    .input-fix-append >>> .el-input-group__append{
        padding:0;
        border:none;
    }

    .input-fix-append >>> .el-input-group__append .ivu-select-selection {
        height: 27px;
        padding-top: 2px;
        border-radius: 3px;
    }

    .payment-nav-tabs >>> .ivu-tabs-nav .ivu-tabs-tab .ivu-icon{
        font-size: 30px !important;
        margin-top: -20px !important;
        margin-right: 20px !important;
    }

    .password_input >>> .el-input-group__prepend{
        padding: 0 10px;
    }

</style>

<template>

    <div>
        <!-- Fade loader - Shows when saving the recurring invoice payment plan  -->
        <fadeLoader :loading="isSavingRecurringPaymentPlan" msg="Saving payment plan, please wait..."></fadeLoader>
        
        <!-- Stage card  -->
        <stagingCard 
            :stageNumber="2" :showCheckMark="localInvoice.has_set_recurring_payment_plan && !isEditingPaymentPlan" :showHeader="false" 
            :disabled="!localInvoice.has_set_recurring_schedule_plan" :showVerticalLine="true" :leftWidth="24"
            :isSaving="isSavingRecurringPaymentPlan">

            <template slot="leftContent">

                <h4 :class="'text-secondary' + ( isEditingPaymentPlan ? ' mt-3 mb-2': '')">Payment Plan:</h4>

                <!-- Manual/Automatic Toggle switch -->
                <toggleSwitch v-if="localInvoice.has_set_recurring_schedule_plan && isEditingPaymentPlan"
                    v-bind:toggleValue.sync="localInvoice.recurringSettings.paymentPlan.automatic"
                    @update:toggleValue="updateToggleChanges($event)"
                    :ripple="false" :showIcon="true" onIcon="ios-cash-outline" offIcon="ios-cash-outline" 
                    title="Automatic Payment:" onText="Yes" offText="No" poptipMsg="Turn on for the system to allow customers to pay using credit cards/mobile phones">
                </toggleSwitch>

                <div v-if="!isEditingPaymentPlan" class="d-inline-block mt-2" :style="{ maxWidth: '80%',lineHeight: '1.6em' }">
                    <p>
                        <b>{{ localInvoice.recurringSettings.paymentPlan.automatic ? 'Automatic': 'Manual' }} Payment:</b> 
                        {{ localInvoice.recurringSettings.paymentPlan.automatic ? 'Allow each invoice to be conveniently paid using mobile money.': 'Notify me on each invoice due, but i will be responsible to collect the money and record payments manually' }}
                    </p>
                    <p v-if="localInvoice.recurringSettings.paymentPlan.automatic"><b>Payment Methods:</b> {{ paymentMethodsInWords }}</p>
                </div>

                <div v-if="localInvoice.has_set_recurring_schedule_plan && isEditingPaymentPlan" class="d-inline-block mt-2 mb-2" :style="{ width: '100%' }">

                    <!-- Payment settings -->
                    <Row v-if="localInvoice.recurringSettings.paymentPlan.automatic" class="mt-2"
                        :style="{ padding: '30px', boxShadow: 'inset 1px 1px 5px 1px #d6d6d6' }">

                        <!-- Payment Methods e.g) Orange Money, MyZaka, e.t.c  -->
                        <Col span="24">
                            <span>
                                <span class="d-inline-block">Customer can pay using:</span>
                                <span class="d-inline-block">
                                    <CheckboxGroup v-model="localInvoice.recurringSettings.paymentPlan.methods"
                                            @on-change="getPaymentMethodsInWords()">
                                        <Checkbox label="OrangeMoney" 
                                                  :disabled="localInvoice.recurringSettings.paymentPlan.methods.length == 1 && 
                                                            localInvoice.recurringSettings.paymentPlan.methods[0] == 'OrangeMoney'">
                                            Orange Money
                                        </Checkbox>
                                        <Checkbox label="MyZaka" 
                                                  :disabled="localInvoice.recurringSettings.paymentPlan.methods.length == 1 && 
                                                             localInvoice.recurringSettings.paymentPlan.methods[0] == 'MyZaka'">
                                        </Checkbox>
                                    </CheckboxGroup>
                                </span>
                            </span>
                            
                            <Tabs :animated="false" class="payment-nav-tabs pt-3" :key="localInvoice.recurringSettings.paymentPlan.methods.length">

                                <!-- Orange Money - Settings -->
                                <TabPane v-if="localInvoice.recurringSettings.paymentPlan.methods.includes('OrangeMoney')" label="Orange Money">
                                    
                                    <Row :gutter="20">
                                        <Col :span="6">
                                            <img style="width: 100%;" src="/images/samples/orange_money_logo.png">
                                        </Col>
                                        <Col :span="18">

                                            <Alert type="success" show-icon class="mb-0">
                                                <i slot="icon" class="icon-wallet icons d-block" :style="{ fontSize:'25px',margin:'10px 0 0 -5px',color:'#ffffff',background:'#ff6600',borderRadius:'50%',padding:'8px' }"></i>
                                                <h4 class="mb-2" :style="{ marginLeft:'45px' }">Account Details</h4> 
                                                <template slot="desc">
                                                    <p><b>Name:</b> Julian B Tabona</p>
                                                    <p><b>Number:</b> (+267) 75993221</p>
                                                    <p><b>Status:</b> Active</p>
                                                </template>
                                            </Alert>
                                            <span class="btn btn-link" @click="isOpenChangeMobileMoneyAccountModal = !isOpenChangeMobileMoneyAccountModal">
                                                Change Account
                                            </span>
                                     
                                        </Col>
                                    </Row>

                                </TabPane>

                                <!-- MyZaka - Settings -->
                                <TabPane v-if="localInvoice.recurringSettings.paymentPlan.methods.includes('MyZaka')" label="MyZaka">
                                    
                                    <Row :gutter="20">
                                        <Col :span="6">
                                            <img style="width: 100%;" src="/images/samples/myzaka_logo.png">
                                        </Col>
                                        <Col :span="18">

                                            <Alert type="success" show-icon class="mb-0">
                                                <i slot="icon" class="icon-wallet icons d-block" :style="{ fontSize:'25px',margin:'10px 0 0 -5px',color:'rgb(238, 29, 35)',background:'#fee600',borderRadius:'50%',padding:'8px' }"></i>
                                                <h4 class="mb-2" :style="{ marginLeft:'45px' }">Account Details</h4> 
                                                <template slot="desc">
                                                    <p><b>Name:</b> Bonolo P Sesiane</p>
                                                    <p><b>Number:</b> (+267) 74647644</p>
                                                    <p><b>Status:</b> Active</p>
                                                </template>
                                            </Alert>
                                            <span class="btn btn-link" @click="isOpenChangeMobileMoneyAccountModal = !isOpenChangeMobileMoneyAccountModal">
                                                Change Account
                                            </span>
                                     
                                        </Col>
                                    </Row>

                                </TabPane>

                            </Tabs>

                        </Col>

                    </Row>
                    
                    <Alert v-else show-icon :style="{ padding: '18px 65px', boxShadow: '#cedee7 1px 1px 3px 1px inset' }">
                        Manual Payment
                        <Icon type="ios-bulb-outline" slot="icon"></Icon>
                        <template slot="desc">Notify me on each invoice due, but i will be responsible to collect the money and record payments manually</template>
                    </Alert>

                </div>

                <div v-if="!localInvoice.has_set_recurring_schedule_plan && !isEditingPaymentPlan" class="mt-3">
                    <Alert show-icon>
                        <Icon type="ios-bulb-outline" slot="icon"></Icon>
                        <template slot="desc">Make paying invoices easy. Setup your invoices to be paid manually/automatically via Orange Money/MyZaka and get notified on every payment made.</template>
                    </Alert>
                </div>

            </template>

            <!-- Extra Content  -->
            <template v-if="localInvoice.has_set_recurring_schedule_plan" slot="extraContent">

                <Row>

                    <Col span="24 mt-2">
                        <Row>
                            <Col span="24">

                                <!-- Focus Ripple  -->
                                <focusRipple color="blue" :ripple="!localInvoice.has_set_recurring_payment_plan" class="float-right">
                                    
                                    <!-- Final Step Button -->
                                    <Button v-if="isEditingPaymentPlan" class="float-right" type="primary" size="large" @click="savePaymentPlan()">
                                        <span>{{ localInvoice.has_set_recurring_payment_plan ? 'Save Changes': 'Final Step' }}</span>
                                    </Button>
                                    
                                    <Button v-else class="float-right" type="default" size="large" @click="activateEditMode()">
                                        <span>Edit Payment</span>
                                    </Button>

                                </focusRipple>
                                
                            </Col>
                        </Row>

                    </Col>

                </Row>

            </template>
            
        </stagingCard>

        <!-- 
            MODAL TO CHANGE MOBILE MONEY ACCOUNT - VIA EMAIL
        -->
        <changeMobileMoneyAccountModal 
            v-if="isOpenChangeMobileMoneyAccountModal" 
            :invoice="localInvoice" 
            @visibility="isOpenChangeMobileMoneyAccountModal = $event"
            @sent="$emit('sent', $event)">
        </changeMobileMoneyAccountModal>

    </div>

</template>
<script type="text/javascript">

    /*  Steps  */
    import stagingCard from './main.vue';

    /*  Loaders  */
    import fadeLoader from './../loaders/fadeLoader.vue';

    /*  Switches  */
    import toggleSwitch from './../../../components/_common/switches/toggleSwitch.vue';

    /*  Inputs   */
    import phoneInput from './../inputs/phoneInput.vue'; 

    /*  Ripples  */
    import focusRipple from './../wavyRipples/focusRipple.vue';

    /*  Modals  */
    import changeMobileMoneyAccountModal from './../../../components/_common/modals/changeMobileMoneyAccountModal.vue';

    import lodash from 'lodash';
    Event.prototype._ = lodash;

    export default {
        components: { fadeLoader, stagingCard, toggleSwitch, phoneInput, focusRipple , changeMobileMoneyAccountModal},
        props: {
            invoice: {
                type: Object,
                default: null
            }
        },
        data(){
            return {
                /*
                    We are using cloneDeep to create a coplete copy of the javascript object without
                    having reactivity to the main invoice. This is so that whatever changes we make to 
                    the localInvoice, they must not affect the parent "invoice". We will only update
                    the parent when we save the changes to the database.
                */
                localInvoice: _.cloneDeep(this.invoice),
                isEditingPaymentPlan: (_.cloneDeep(this.invoice).recurringSettings.editing.paymentPlan),
                isSavingRecurringPaymentPlan: false,
                isOpenChangeMobileMoneyAccountModal: false,

                paymentMethodsInWords: ''
            }
        },
        watch: {

            //  Watch for changes on the invoice
            invoice: {
                handler: function (val, oldVal) {
                    
                    //  Update the local invoice value
                    this.localInvoice = _.cloneDeep(val);

                    //  Update the editing payment shortcut
                    this.isEditingPaymentPlan = (_.cloneDeep(val).recurringSettings.editing.paymentPlan);

                },
                deep: true
            }
        },
        methods: {
            updateToggleChanges(newVal){
                this.localInvoice.recurringSettings.paymentPlan.automatic = newVal;
                this.getPaymentMethodsInWords();
            },
            getPaymentMethodsInWords(){
                var paymentMethods = this.localInvoice.recurringSettings.paymentPlan.methods;
                var inWords = '';

                for(var x=0; x < paymentMethods.length; x++){
                    inWords+= paymentMethods[x];

                    if(x == (paymentMethods.length - 2)){
                        inWords+=' & '; 
                    }else if( x < (paymentMethods.length - 1) ){
                        inWords+=', ';
                    }
                }
                this.paymentMethodsInWords = inWords;
            },
            activateEditMode(){
                //  Get all the plans and their edit state
                var editingSchedulePlan = ( this.localInvoice.recurringSettings.editing.schedulePlan );
                var editingDeliveryPlan = ( this.localInvoice.recurringSettings.editing.deliveryPlan );
                var editingPaymentPlan = ( this.localInvoice.recurringSettings.editing.paymentPlan );

                //  If we are still editing the schedule/delivery plan 
                if( editingSchedulePlan || editingDeliveryPlan ){
                    //  Tell the user to save first before editing
                    this.$Notice.warning({
                        title: 'Save '+(editingSchedulePlan ? 'Schedule Plans': 'Delivery Plans')+'!',
                        desc: 'Save your '+(editingSchedulePlan ? 'Schedule Plans': 'Delivery Plans')+' first before editing your Payment Plans',
                    });
                }else{
                    this.localInvoice.recurringSettings.editing.paymentPlan = true;
                    this.isEditingPaymentPlan = true;
                }
            },
            savePaymentPlan(){

                var self = this;

                //  Start loader
                self.isSavingRecurringPaymentPlan = true;

                console.log('Attempt to save recurring payment plan...');

                //  Form data to send
                let invoiceData = { invoice: self.localInvoice };

                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/invoices/'+self.localInvoice.id+'/recurring/update-payment-plan', invoiceData)
                    .then(({ data }) => {
                        
                        console.log(data);

                        //  Stop loader
                        self.isSavingRecurringPaymentPlan = false;
                        
                        //  Alert creation success
                        self.$Message.success('Payment plan saved sucessfully!');

                        self.$emit('saved', data);

                    })         
                    .catch(response => { 
                        //  Stop loader
                        self.isSavingRecurringPaymentPlan = false;

                        console.log('invoiceRecurringPaymentPlanStage.vue - Error saving recurring schedule payment plan...');
                        console.log(response);
                    });
            }
        },
        created(){
            this.getPaymentMethodsInWords();
        }
    }
</script>
