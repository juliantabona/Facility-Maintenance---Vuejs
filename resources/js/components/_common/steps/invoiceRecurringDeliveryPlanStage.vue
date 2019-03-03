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

    .delivery-nav-tabs >>> .ivu-tabs-nav .ivu-tabs-tab .ivu-icon{
        font-size: 30px !important;
        margin-top: -20px !important;
        margin-right: 20px !important;
    }

</style>

<template>

    <div>

        <!-- Fade loader - Shows when saving the recurring invoice delivery plan  -->
        <fadeLoader :loading="isSavingRecurringDeliveryPlan" msg="Saving delivery plan, please wait..."></fadeLoader>

        <!-- Stage card  -->
        <stagingCard 
            :stageNumber="3" :showCheckMark="localInvoice.has_set_recurring_delivery_plan && !isEditingDeliveryPlan" :showHeader="false" 
            :disabled="!localInvoice.has_set_recurring_payment_plan" :showVerticalLine="true" :leftWidth="24"
            :isSaving="isSavingRecurringDeliveryPlan">

            <template slot="leftContent">

                <h4 :class="'text-secondary' + ( isEditingDeliveryPlan ? ' mt-3 mb-2': '')">Delivery Plan:</h4>

                <!-- Manual/Automatic Toggle switch -->
                <toggleSwitch v-if="localInvoice.has_set_recurring_payment_plan && isEditingDeliveryPlan"
                    v-bind:toggleValue.sync="localInvoice.recurringSettings.deliveryPlan.automatic"
                    @update:toggleValue="updateToggleChanges($event)"
                    :ripple="false" :showIcon="true" onIcon="ios-send-outline" offIcon="ios-eye-outline" 
                    title="Send Automatically:" onText="Yes" offText="No" poptipMsg="Turn on for the system to send email/sms automatically">
                </toggleSwitch>

                <div v-if="!isEditingDeliveryPlan" class="d-inline-block mt-2" :style="{ maxWidth: '80%',lineHeight: '1.6em' }">
                    <p>
                        <b>{{ localInvoice.recurringSettings.deliveryPlan.automatic ? 'Automatic': 'Manual' }} Sending:</b> 
                        {{ localInvoice.recurringSettings.deliveryPlan.automatic ? 'Automatically send each invoice to my customer.': 'Notify me on each invoice due, but i will be responsible to send it manually' }}
                    </p>
                    <p v-if="localInvoice.recurringSettings.deliveryPlan.automatic"><b>Delivery Methods:</b> {{ deliveryMethodsInWords }}</p>
                </div>

                <div v-if="localInvoice.has_set_recurring_payment_plan && isEditingDeliveryPlan" class="d-inline-block mt-2 mb-2" :style="{ width: '100%' }">

                    <!-- Delivery settings -->
                    <Row v-if="localInvoice.recurringSettings.deliveryPlan.automatic" class="mt-2"
                        :style="{ padding: '30px', boxShadow: 'inset 1px 1px 5px 1px #d6d6d6' }">

                        <!-- Schedule Frequency e.g) Daily, Weekly, Monthly, Yearly or Custom  -->
                        <Col span="24">
                            <span>
                                <span class="d-inline-block">Delivery Methods:</span>
                                <span class="d-inline-block">
                                    <CheckboxGroup v-model="localInvoice.recurringSettings.deliveryPlan.methods"
                                        @on-change="getDeliveryMethodsInWords()">
                                        <Checkbox label="Email" :disabled="localInvoice.recurringSettings.deliveryPlan.methods.length == 1 && localInvoice.recurringSettings.deliveryPlan.methods[0] == 'Email'"></Checkbox>
                                        <Checkbox label="Sms" :disabled="localInvoice.recurringSettings.deliveryPlan.methods.length == 1 && localInvoice.recurringSettings.deliveryPlan.methods[0] == 'Sms'"></Checkbox>
                                    </CheckboxGroup>
                                </span>
                            </span>
                            <Tabs :animated="false" class="delivery-nav-tabs pt-3" :key="localInvoice.recurringSettings.deliveryPlan.methods.length">

                                <TabPane v-if="localInvoice.recurringSettings.deliveryPlan.methods.includes('Sms')" label="SMS Delivery" 
                                         icon="ios-phone-portrait" class="mt-4">
                                    <Row :gutter="20">
                                        <Col :span="10">
                                            <img style="width: 100%;" src="/images/samples/phone_animation.png">
                                        </Col>
                                        <Col :span="14">
                                            <Alert show-icon>
                                                <Icon type="ios-bulb-outline" slot="icon"></Icon>
                                                <template slot="desc">Enter the client mobile number(s). When the invoice is due it will be sent to all active numbers via SMS. Only numbers set to "Active" will receive the sms.</template>
                                            </Alert>
                                            
                                            <b>Client Mobile Number(s)</b>
                                            <!-- Client Phones editor -->
                                            <phoneInput class="mb-2"  
                                                        :modelId="localInvoice.customized_client_details.id" 
                                                        :modelType="localInvoice.customized_client_details.model_type" 
                                                        :phones="localInvoice.recurringSettings.deliveryPlan.sms.phones" 
                                                        :numberLimit="3"
                                                        selectedType="mobile"
                                                        :disabledTypes="['tel', 'fax']"                                                        
                                                        :removable="true"
                                                        :deletable="false"
                                                        :hidedable="true"
                                                        :editable="true"
                                                        :removeDuplicates="true"
                                                        :showIcon="true" 
                                                        onIcon="ios-checkmark" offIcon="" 
                                                        title="Active:" onText="Yes" offText="No" 
                                                        poptipMsg="Turn on to send the sms to this number"
                                                        @updated="localInvoice.recurringSettings.deliveryPlan.sms.phones = $event">
                                            </phoneInput>

                                            <b class="d-block mt-3">Delivery Message:</b>
                                            <p :style="{ padding: '20px', boxShadow: 'inset 1px 1px 5px 1px #d6d6d6' }">
                                                {{ smsMessageCompiled }}
                                            </p>

                                            <span :class="(smsMessageCompiled.length <= 160 ? 'text-success': 'text-danger') + ' text-right float-right mt-2'">Sms Characters {{ smsMessageCompiled.length }}/160</span>
                                            <span class="btn btn-link float-right">Edit Message</span>
                                            <div class="clearfix"></div>
                                            <Button type="primary" class="float-right mt-2" @click="isOpenSendTestSmsModal = !isOpenSendTestSmsModal">
                                                <span>Send Test SMS</span>
                                                <Icon type="ios-send-outline" :size="24" :style="{ marginTop: '-4px' }"/>
                                            </Button>
                                        </Col>
                                    </Row>
                                </TabPane>

                                <TabPane v-if="localInvoice.recurringSettings.deliveryPlan.methods.includes('Email')" label="Email Delivery" 
                                         icon="ios-mail-outline" class="mt-4">
                                    

                                    <!-- Email Address -->
                                    <Row :gutter="20" class="mt-1">
                                        <Col :span="24">
                                            <span class="d-block font-weight-bold mb-1">Send to:</span>
                                            <el-input placeholder="Recipient email e.g) example@gmail.com" v-model="localInvoice.recurringSettings.deliveryPlan.mail.email" size="mini" class="mb-1"></el-input>
                                        </Col>
                                    </Row>

                                    <!-- Email Subject -->
                                    <Row :gutter="20">
                                        
                                        <Col :span="24">
                                            <span class="d-block font-weight-bold mt-2 mb-1">Subject:</span>
                                            <el-input placeholder="Email Subject" v-model="localInvoice.recurringSettings.deliveryPlan.mail.subject" 
                                                    size="mini" class="input-fix-append mb-1">
                                                <shortCodeSelector slot="append"
                                                    :style="{ marginLeft: '5px' }"
                                                    :shortCodes="shortCodes" @selected="localInvoice.recurringSettings.deliveryPlan.mail.subject += $event">
                                                </shortCodeSelector>
                                            </el-input>

                                        </Col>
                                    </Row>
                                    
                                    <!-- Email Message -->
                                    <Row :gutter="20">
                                        <Col :span="24">
                                            <span class="d-inline-block font-weight-bold mt-3 mb-2">
                                                Message:
                                            </span>
                                            <shortCodeSelector
                                                :shortCodes="shortCodes" @selected="localInvoice.recurringSettings.deliveryPlan.mail.message += $event">
                                            </shortCodeSelector>
                                            <froalaEditor :content.sync="localInvoice.recurringSettings.deliveryPlan.mail.message" 
                                                          :height="200" :heightMax="300"></froalaEditor>                    
                                        </Col>
                                    </Row>

                                </TabPane>

                            </Tabs>

                        </Col>

                    </Row>

                    <Alert v-else show-icon :style="{ padding: '18px 65px', boxShadow: '#cedee7 1px 1px 3px 1px inset' }">
                        Manual Sending
                        <Icon type="ios-bulb-outline" slot="icon"></Icon>
                        <template slot="desc">Only notify me but i will be the one to send this invoice</template>
                    </Alert>
                </div>

                <div v-if="!localInvoice.has_set_recurring_payment_plan && !isEditingDeliveryPlan" class="mt-3">
                    <Alert show-icon>
                        <Icon type="ios-bulb-outline" slot="icon"></Icon>
                        <template slot="desc">Make delivering invoices easy. Setup your invoices to be sent manually/automatically via email/sms and get notified on delivery.</template>
                    </Alert>
                </div>

            </template>

            <!-- Extra Content  -->
            <template v-if="localInvoice.has_set_recurring_payment_plan" slot="extraContent">

                <Row>

                    <Col span="24 mt-2">
                        <Row>
                            <Col span="24">

                                <!-- Final Step Button -->
                                <Button v-if="isEditingDeliveryPlan" class="float-right" type="primary" size="large" @click="saveDeliveryPlan()">
                                    <span>{{ localInvoice.has_set_recurring_delivery_plan ? 'Save Changes': 'Done' }}</span>
                                </Button>
                                <Button v-else class="float-right" type="default" size="large" @click="activateEditMode()">
                                    <span>Edit Delivery</span>
                                </Button>
                                
                            </Col>
                        </Row>

                    </Col>

                </Row>

            </template>
            
        </stagingCard>

        <!-- 
            MODAL TO SEND TEST SMS
        -->
        <sendTestSmsModal 
            v-if="isOpenSendTestSmsModal" 
            :message="smsMessageCompiled" 
            :url="'/api/invoices/'+localInvoice.id+'/recurring/send/sms?test=1'"
            @visibility="isOpenSendTestSmsModal = $event"
            @sent="$emit('sent', $event)">
        </sendTestSmsModal>

    </div>

</template>
<script type="text/javascript">

    import moment from 'moment';

    /*  Steps  */
    import stagingCard from './main.vue';

    /*  Loaders  */
    import fadeLoader from './../loaders/fadeLoader.vue';

    /*  Selectors  */
    import shortCodeSelector from './../selectors/shortCodeSelector.vue';

    /*  Switches  */
    import toggleSwitch from './../../../components/_common/switches/toggleSwitch.vue';

    /*  Selectors  */
    import animatedCheckmark from './../animatedIcons/animatedCheckmark.vue';

    /*  Editors  */
    import froalaEditor from './../wiziwigEditors/froalaEditor.vue'; 
    
    /*  Ripples  */
    import focusRipple from './../wavyRipples/focusRipple.vue';

    /*  Modals  */
    import sendTestSmsModal from './../modals/sendTestSmsModal.vue';

    /*  Inputs   */
    import phoneInput from './../inputs/phoneInput.vue'; 

    import lodash from 'lodash';
    Event.prototype._ = lodash;

    export default {
        components: { fadeLoader, stagingCard, shortCodeSelector, toggleSwitch, focusRipple, froalaEditor, sendTestSmsModal,
                      phoneInput 
        },
        props: {
            invoice: {
                type: Object,
                default: null
            }
        },
        data(){
            return {
                moment: moment,

                /*
                    We are using cloneDeep to create a coplete copy of the javascript object without
                    having reactivity to the main invoice. This is so that whatever changes we make to 
                    the localInvoice, they must not affect the parent "invoice". We will only update
                    the parent when we save the changes to the database.
                */
                localInvoice: _.cloneDeep(this.invoice),
                isEditingDeliveryPlan: (_.cloneDeep(this.invoice).recurringSettings.editing.deliveryPlan),

                isOpenSendTestSmsModal: false,
                isSavingRecurringDeliveryPlan: false,
                deliveryMethodsInWords: '',

                shortCodes: this.getShotCodes(),
                subjectShortCode: '',
                messageShortCode: '',
    
            } 
        },
        watch: {

            //  Watch for changes on the invoice
            invoice: {
                handler: function (val, oldVal) {
                    
                    //  Update the local invoice value
                    this.localInvoice = _.cloneDeep(val);

                    //  Update the editing payment shortcut
                    this.isEditingDeliveryPlan = (_.cloneDeep(val).recurringSettings.editing.deliveryPlan);

                },
                deep: true
            }
        },
        computed: {
            getSelectedPhone(newPhone){
                this.localInvoice.recurringSettings.deliveryPlan.sms.phones.push(newPhone)
            },
            smsMessageCompiled: function(){

                var referenceNo = this.invoice.reference_no_value;
                var items = '';
                var currency = (((this.invoice || {}).currency_type || {}).currency || {}).symbol || '';
                var grand_total = this.formatPrice( (this.invoice.grand_total_value || 0), currency);
                var expiry_date = moment(this.invoice.expiry_date_value).format('MMM DD YYYY');
                var client = ((this.invoice || {}).customized_client_details || {});
                var company = ((this.invoice || {}).customized_company_details || {});

                for( var x = 0; x < this.invoice.items.length; x++  ){
                    x == 0 ? items += '' : items +=' ';
                    items += ( (this.invoice.items[x].quantity) +'x '+(this.invoice.items[x].name) );
                }

                var characterLimit = 160;
                //  Company info text limit = 23
                var companyName = this.truncate(company.name.trim(), 21) + ( company.name.length <= 21 ? ':' : '' );       //  Optimum Quality: 
                //  Reference text limit = 16
                var reference = 'Invoice #'+referenceNo;                        //  Invoice #002
                //  Amount text limit = 20
                var amount = 'Amount ' + grand_total;                           //  Amount P350.00
                //  Due date text limit = 21
                var dueDate = ' due '+expiry_date;                              //  due on 15 Feb 2018
                //  Reply for payment text limit = 32
                var replyWith = '.Reply with '+referenceNo+'#<pin> to pay';     //  Reply with 002#<pin> to pay
                
                //  items text limit = 49
                var charLeft = (characterLimit - (companyName+reference+amount+dueDate+replyWith).length);
                var items = this.truncate(' for ' + items + ( items.length <= charLeft ? '.' : '' ) , charLeft);    //  for 1x Basic Website, 1x Web Hosting, 5x Emails. 

                var message = companyName+reference+items+amount+dueDate+replyWith;

                //  Update the local invoice message
                this.localInvoice.recurringSettings.deliveryPlan.sms.message = message;

                //  Return the compiled message
                return message;
            },
            emailMessageCompiled: function(){

                var message = this.localInvoice.recurringSettings.deliveryPlan.mail.message;

                var shortCodeSymbols = Object.keys(this.shortCodes);
                var shortCodeValues = Object.values(this.shortCodes);

                var replaceThis, withThis;

                for(var x=0; x < _.size(this.shortCodes); x++){

                    replaceThis = shortCodeSymbols[x]; 
                    withThis = shortCodeValues[x];    
                    message = message.split(replaceThis).join(withThis);

                }

                //  Update the local invoice message
                this.localInvoice.recurringSettings.deliveryPlan.mail.message = message;

                //  Return the compiled message
                return message;
            }   
        },
        methods: {
            updateToggleChanges(newVal){
                this.localInvoice.recurringSettings.deliveryPlan.automatic = newVal;
                this.getDeliveryMethodsInWords();
            },
            getDeliveryMethodsInWords(){
                var devliveryMethods = this.localInvoice.recurringSettings.deliveryPlan.methods;
                var inWords = '';

                for(var x=0; x < devliveryMethods.length; x++){
                    inWords+= devliveryMethods[x];

                    if(x == (devliveryMethods.length - 2)){
                        inWords+=' & '; 
                    }else if( x < (devliveryMethods.length - 1) ){
                        inWords+=', ';
                    }
                }
                this.deliveryMethodsInWords = inWords;
            },
            truncate(string, limit){
                return (string.length > limit) ? string.substring(0, limit - 3)+'...' : string;
            },
            getShotCodes(){

                var money = this.invoice.grand_total_value || 0;
                var currency = (((this.invoice || {}).currency_type || {}).currency || {}).symbol || '';
                var sub_total = this.formatPrice( (this.invoice.sub_total_value || 0), currency);
                var grand_total = this.formatPrice( (this.invoice.grand_total_value || 0), currency);
                var client = ((this.invoice || {}).customized_client_details || {});
                var company = ((this.invoice || {}).customized_company_details || {});

                var shortCodes = {
                    '[invoice_heading]': this.invoice.heading,
                    '[invoice_reference_no]': '#'+this.invoice.reference_no_value,
                    '[created_date]': moment(this.invoice.created_date_value).format('MMM DD YYYY'),
                    '[expiry_date]': moment(this.invoice.expiry_date_value).format('MMM DD YYYY'),
                    '[sub_total]': sub_total,
                    '[grand_total]': grand_total,
                    '[currency]': currency,
                    '[client_company_name]': client.name,
                    '[client_first_name]': client.first_name,
                    '[client_last_name]': client.last_name,
                    '[client_full_name]': client.full_name,
                    '[client_email]': client.email,
                    '[my_company_name]': company.name,
                    '[my_company_email]': company.email,
                };

                if( client.model_type == 'user' ){
                    delete shortCodes['[client_company_name]']; 
                }else if( client.model_type == 'company' ){
                    delete shortCodes['[client_first_name]'];                     
                    delete shortCodes['[client_last_name]'];                     
                    delete shortCodes['[client_full_name]'];                        
                }

                return shortCodes;
            },
            formatPrice(money, symbol) {
                let val = (money/1).toFixed(2).replace(',', '.');
                return symbol + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            },
            activateEditMode(){
                //  Get all the plans and their edit state
                var editingSchedulePlan = ( this.localInvoice.recurringSettings.editing.schedulePlan );
                var editingDeliveryPlan = ( this.localInvoice.recurringSettings.editing.deliveryPlan );
                var editingPaymentPlan = ( this.localInvoice.recurringSettings.editing.paymentPlan );
                console.log('************************************************************* ');
                console.log('editingDeliveryPlan1: ' + this.isEditingDeliveryPlan);

                //  If we are still editing the schedule/payment plan 
                if( editingSchedulePlan || editingPaymentPlan ){
                    //  Tell the user to save first before editing
                    this.$Notice.warning({
                        title: 'Save '+(editingSchedulePlan ? 'Schedule Plans': 'Payment Plans')+'!',
                        desc: 'Save your '+(editingSchedulePlan ? 'Schedule Plans': 'Payment Plans')+' first before editing your Schedule Plans',
                    });
                }else{
                    this.localInvoice.recurringSettings.editing.deliveryPlan = true;
                    this.isEditingDeliveryPlan = true;
                }

                console.log('************************************************************* ');
                console.log('editingDeliveryPlan2: ' + this.isEditingDeliveryPlan);

            },
            saveDeliveryPlan(){

                var self = this;

                //  Start loader
                self.isSavingRecurringDeliveryPlan = true;

                console.log('Attempt to save recurring delivery plan...');

                //  Form data to send
                let invoiceData = { invoice: self.localInvoice };

                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/invoices/'+self.localInvoice.id+'/recurring/update-delivery-plan', invoiceData)
                    .then(({ data }) => {
                        
                        console.log(data);

                        //  Stop loader
                        self.isSavingRecurringDeliveryPlan = false;
                        
                        //  Alert creation success
                        self.$Message.success('Delivery plan saved sucessfully!');

                        self.$emit('saved', data);

                    })         
                    .catch(response => { 
                        //  Stop loader
                        self.isSavingRecurringDeliveryPlan = false;

                        console.log('recurringSettingsStage.vue - Error saving recurring schedule delivery plan...');
                        console.log(response);
                    });
            }
        },
        created(){
            this.getDeliveryMethodsInWords();

            this.localInvoice.recurringSettings.deliveryPlan.mail.subject = 'Invoice [invoice_reference_no]';
            this.localInvoice.recurringSettings.deliveryPlan.mail.email = this.localInvoice.customized_client_details.email;
            this.localInvoice.recurringSettings.deliveryPlan.mail.message = 
                '<p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:12px;font-family:arial, \'helvetica neue\', helvetica, sans-serif;line-height:18px;color:#000000;">  \
                    Good day,  \
                </p> \
                <br> \
                <p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:12px;font-family:arial, \'helvetica neue\', helvetica, sans-serif;line-height:18px;color:#000000;">  \
                    Please find attached <strong>Invoice [invoice_reference_no]</strong> \
                    created on your account for services rendered. Payment regarding the&nbsp;balance of  \
                    <strong>[grand_total] </strong> \
                    must be settled by the  \
                    <strong>[expiry_date]</strong>  \
                    or earlier. \
                </p> \
                <br> \
                <p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:12px;font-family:arial, \'helvetica neue\', helvetica, sans-serif;line-height:18px;color:#000000;">  \
                    We look forward to conducting future business with you. \
                </p> \
                <br> \
                <p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:12px;font-family:arial, \'helvetica neue\', helvetica, sans-serif;line-height:18px;color:#000000;">  \
                    Regards, \
                    <br> \
                    [my_company_name] \
                </p>';
        }
    }
</script>
