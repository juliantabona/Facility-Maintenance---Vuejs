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
                    v-bind:toggleValue.sync="(localInvoice.recurringSettings.paymentPlan.automatic == 'true') ? true : false"
                    @update:toggleValue="localInvoice.recurringSettings.paymentPlan.automatic = (($event) ? 'true' : 'false'))"
                    :ripple="false" :showIcon="true" onIcon="ios-cash-outline" offIcon="ios-cash-outline" 
                    title="Automatic Payment:" onText="Yes" offText="No" poptipMsg="Turn on for the system to allow customers to pay using credit cards/mobile phones">
                </toggleSwitch>

                <div v-if="!isEditingPaymentPlan" class="d-inline-block mt-2" :style="{ lineHeight: '1.6em' }">
                    <p>
                        <b>{{ localInvoice.recurringSettings.paymentPlan.automatic == 'true' ? 'Automatic': 'Manual' }} Payment:</b> 
                        {{ localInvoice.recurringSettings.paymentPlan.automatic == 'true' ? 'The system will allow each invoice to be conveniently paid using mobile money/credit cards.': 'Notify me on each invoice due, but i will be responsible to collect the money and record payments manually' }}
                    </p>
                    <p><b>Payment Methods:</b> Orange Money & MyZaka</p>
                    <p><b>Alerts:</b> Notify me via Email and Sms when each invoice is paid.</p>
                </div>

                <div v-if="localInvoice.has_set_recurring_schedule_plan && isEditingPaymentPlan" class="d-inline-block mt-2 mb-2" :style="{ width: '100%' }">

                    <!-- Payment settings -->
                    <Row v-if="localInvoice.recurringSettings.paymentPlan.automatic == 'true'" class="mt-2"
                        :style="{ padding: '30px', boxShadow: 'inset 1px 1px 5px 1px #d6d6d6' }">

                        <!-- Payment Methods e.g) Orange Money, MyZaka, e.t.c  -->
                        <Col span="24">
                            <span>
                                <span class="d-inline-block">Payment Methods:</span>
                                <span class="d-inline-block">
                                    <CheckboxGroup v-model="localInvoice.recurringSettings.paymentPlan.methods">
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
                                            <Alert show-icon>
                                                <Icon type="ios-bulb-outline" slot="icon"></Icon>
                                                <template slot="desc">Enter your orange money mobile number and pin to activate payment using Orange Money directly to your account.</template>
                                            </Alert>
                                            
                                            <div :style="{ padding: '30px', boxShadow: 'inset 1px 1px 5px 1px #d6d6d6' }">
                                                <b>Phone Number</b>
                                                <!-- Orange Money Phone Number editor -->
                                                <phoneInput class="mb-2"  
                                                            :modelId="localInvoice.customized_client_details.id" 
                                                            :modelType="localInvoice.customized_client_details.model_type" 
                                                            :phones="[]" 
                                                            :numberLimit="1"
                                                            selectedType="mobile"
                                                            :disabledTypes="['Telephone', 'Fax']"                                                        :deletable="false"
                                                            :hidedable="true"
                                                            :editable="true"
                                                            :showIcon="true" 
                                                            onIcon="ios-checkmark" offIcon="" 
                                                            title="Active:" onText="Yes" offText="No" 
                                                            poptipMsg="Turn on to send the sms to this number"
                                                            @updated="localInvoice.recurringSettings.deliveryPlan.sms.phones = $event">
                                                </phoneInput>

                                                <b class="d-block mt-2 mb-1">Pin (4 digits):</b>
                                                <el-input type="password" :maxlength="4" size="mini" class="password_input mb-1" placeholder="Enter Orange Money Pin">
                                                    <Icon slot="prepend" type="ios-lock-outline" :size="20"/>
                                                </el-input>
                                                <Button type="primary" class="float-right mt-2">
                                                    <span>Connect</span>
                                                    <Icon type="ios-repeat" :size="24" :style="{ marginTop: '-4px' }"/>
                                                </Button>
                                                <div class="clearfix"></div>
                                            </div>
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
                                            <Alert show-icon>
                                                <Icon type="ios-bulb-outline" slot="icon"></Icon>
                                                <template slot="desc">Enter your Mascom mobile number and MyZaka pin to activate payment using MyZaka directly to your account.</template>
                                            </Alert>
                                            
                                            <div :style="{ padding: '30px', boxShadow: 'inset 1px 1px 5px 1px #d6d6d6' }">
                                                <b>Phone Number</b>
                                                <!-- Mascom Phone Number editor -->
                                                <phoneInput class="mb-2"  
                                                            :modelId="localInvoice.customized_client_details.id" 
                                                            :modelType="localInvoice.customized_client_details.model_type" 
                                                            :phones="[]" 
                                                            :numberLimit="1"
                                                            selectedType="mobile"
                                                            :disabledTypes="['Telephone', 'Fax']"                                                        
                                                            :deletable="false"
                                                            :hidedable="true"
                                                            :editable="true"
                                                            :showIcon="true" 
                                                            onIcon="ios-checkmark" offIcon="" 
                                                            title="Active:" onText="Yes" offText="No" 
                                                            poptipMsg="Turn on to send the sms to this number"
                                                            @updated="localInvoice.recurringSettings.deliveryPlan.sms.phones = $event">
                                                </phoneInput>

                                                <b class="d-block mt-2 mb-1">Pin (4 digits):</b>
                                                <el-input type="password" :maxlength="4" size="mini" class="password_input mb-1" placeholder="Enter MyZaka Pin">
                                                    <Icon slot="prepend" type="ios-lock-outline" :size="20"/>
                                                </el-input>
                                                <Button type="primary" class="float-right mt-2">
                                                    <span>Connect</span>
                                                    <Icon type="ios-repeat" :size="24" :style="{ marginTop: '-4px' }"/>
                                                </Button>
                                                <div class="clearfix"></div>
                                            </div>
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

                                <!-- Final Step Button -->
                                <Button v-if="isEditingPaymentPlan" class="float-right" type="primary" size="large" @click="savePaymentPlan()">
                                    <span>{{ localInvoice.has_set_recurring_payment_plan ? 'Save Changes': 'Final Step' }}</span>
                                </Button>
                                <Button v-else class="float-right" type="default" size="large" @click="activateEditMode()">
                                    <span>Edit Payment</span>
                                </Button>
                                
                            </Col>
                        </Row>

                    </Col>

                </Row>

            </template>
            
        </stagingCard>

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

    import lodash from 'lodash';
    Event.prototype._ = lodash;

    export default {
        components: { fadeLoader, stagingCard, toggleSwitch, phoneInput },
        props: {
            invoice: {
                type: Object,
                default: null
            }
        },
        data(){
            return {
                isSavingRecurringPaymentPlan: false,
                localInvoice: this.invoice,
                isEditingPaymentPlan: (this.invoice.recurringSettings.editing.paymentPlan == 'true'),
            }
        },
        watch: {

            //  Watch for changes on the invoice
            invoice: {
                handler: function (val, oldVal) {

                    //  Update the local invoice value
                    this.localInvoice = val;

                    //  Update the editing payment shortcut
                    this.isEditingPaymentPlan = (val.recurringSettings.editing.paymentPlan == 'true')

                },
                deep: true
            }
        },
        methods: {
            activateEditMode(){
                //  Get all the plans and their edit state
                //  JSON.parse converts the 'true/false' string to Boolean
                
                var editingSchedulePlan = ( this.localInvoice.recurringSettings.editing.schedulePlan == 'true' );
                var editingDeliveryPlan = ( this.localInvoice.recurringSettings.editing.deliveryPlan == 'true' );
                var editingPaymentPlan = ( this.localInvoice.recurringSettings.editing.paymentPlan == 'true' );

                //  If we are still editing the schedule/delivery plan 
                if( editingSchedulePlan || editingDeliveryPlan ){
                    //  Tell the user to save first before editing
                    this.$Notice.warning({
                        title: 'Save '+(editingSchedulePlan ? 'Schedule Plans': 'Delivery Plans')+'!',
                        desc: 'Save your '+(editingSchedulePlan ? 'Schedule Plans': 'Delivery Plans')+' first before editing your Payment Plans',
                    });
                }else{
                    this.localInvoice.recurringSettings.editing.paymentPlan = 'true';
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

                        self.$emit('approved', data);

                    })         
                    .catch(response => { 
                        //  Stop loader
                        self.isSavingRecurringPaymentPlan = false;

                        console.log('invoiceRecurringPaymentPlanStage.vue - Error saving recurring schedule payment plan...');
                        console.log(response);
                    });
            }
        }
    }
</script>
