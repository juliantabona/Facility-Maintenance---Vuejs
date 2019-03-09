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
                        
                            <deliveryWidget 
                                :deliveryMethods="localInvoice.recurringSettings.deliveryPlan.methods"
                                :clientDetails="localInvoice.customized_client_details"
                                :deliveryPhones="localInvoice.recurringSettings.deliveryPlan.sms.phones"
                                :deliveryMailAddress="localInvoice.recurringSettings.deliveryPlan.mail.email"
                                :deliveryMailSubject="localInvoice.recurringSettings.deliveryPlan.mail.subject"
                                :deliveryMailMessage="localInvoice.recurringSettings.deliveryPlan.mail.message"
                                :deliverySmsMessage="smsMessageCompiled"
                                :testSmsUrl="'/api/invoices/'+localInvoice.id+'/send?test=1'"
                                :testEmailUrl="'/api/invoices/'+localInvoice.id+'/send?test=1'"
                                :shortcodes="shortcodes"
                                @updatedDeliveryMethods="getDeliveryMethodsInWords()"
                                @updated:stage=""
                                @updated:deliveryPhones="localInvoice.recurringSettings.deliveryPlan.sms.phones = $event"
                                @updated:deliveryMailAddress="localInvoice.recurringSettings.deliveryPlan.mail.email = $event"
                                @updated:deliveryMailSubject="localInvoice.recurringSettings.deliveryPlan.mail.subject = $event"
                                @updated:deliveryMailMessage="localInvoice.recurringSettings.deliveryPlan.mail.message = $event">

                            </deliveryWidget>

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

    </div>

</template>
<script type="text/javascript">

    import moment from 'moment';

    /*  Steps  */
    import stagingCard from './main.vue';

    /*  Loaders  */
    import fadeLoader from './../loaders/fadeLoader.vue';

    /*  Switches  */
    import toggleSwitch from './../../../components/_common/switches/toggleSwitch.vue';

    /*  Selectors  */
    import animatedCheckmark from './../animatedIcons/animatedCheckmark.vue';
    
    /*  Ripples  */
    import focusRipple from './../wavyRipples/focusRipple.vue';

    import deliveryWidget from './../../../widgets/delivery/show/main.vue';

    import lodash from 'lodash';
    Event.prototype._ = lodash;

    export default {
        components: { fadeLoader, stagingCard, toggleSwitch, focusRipple, deliveryWidget
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

                isSavingRecurringDeliveryPlan: false,
                deliveryMethodsInWords: '',

                shortcodes: this.getShotCodes(),
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

                var shortcodesymbols = Object.keys(this.shortcodes);
                var shortCodeValues = Object.values(this.shortcodes);

                var replaceThis, withThis;

                for(var x=0; x < _.size(this.shortcodes); x++){

                    replaceThis = shortcodesymbols[x]; 
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

                var shortcodes = {
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
                    delete shortcodes['[client_company_name]']; 
                }else if( client.model_type == 'company' ){
                    delete shortcodes['[client_first_name]'];                     
                    delete shortcodes['[client_last_name]'];                     
                    delete shortcodes['[client_full_name]'];                        
                }

                return shortcodes;
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
        }
    }
</script>
