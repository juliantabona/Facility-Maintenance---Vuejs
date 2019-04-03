<template>

    <div>

        <!-- Fade loader - Shows when saving the recurring delivery plan  -->
        <fadeLoader :loading="isSavingRecurringDeliveryPlan" msg="Saving delivery plan, please wait..."></fadeLoader>

        <!-- Stage card  -->
        <stagingCard 
            :stageNumber="3" 
            :showHeader="showHeader"
            :disabled="disabled" 
            :showVerticalLine="true" :leftWidth="24"
            :isSaving="isSavingRecurringDeliveryPlan">

            <template slot="leftContent">

                <h4 :class="'text-secondary' + ( isEditingStage ? ' mt-3 mb-2': '')">Delivery Plan:</h4>

                <!-- Manual/Automatic Toggle switch -->
                <toggleSwitch v-if="showToggleSwitch"
                    v-bind:toggleValue.sync="(localRecurringSettings.deliveryPlan || {}).automatic"
                    @update:toggleValue="updateToggleChanges($event)"
                    :ripple="false" :showIcon="true" onIcon="ios-send-outline" offIcon="ios-eye-outline" 
                    title="Send Automatically:" onText="Yes" offText="No" poptipMsg="Turn on for the system to send email/sms automatically">
                </toggleSwitch>

                <div v-if="!isEditingStage" class="d-inline-block mt-2" :style="{ maxWidth: '80%',lineHeight: '1.6em' }">
                    <p>
                        <b>{{ (localRecurringSettings.deliveryPlan || {}).automatic ? 'Automatic': 'Manual' }} Sending:</b> 
                        {{ (localRecurringSettings.deliveryPlan || {}).automatic ? 'Automatically send each '+resourceName+' to my customer.': 'Notify me on each '+resourceName+' due, but i will be responsible to send it manually' }}
                    </p>
                    <p v-if="(localRecurringSettings.deliveryPlan || {}).automatic"><b>Delivery Methods:</b> {{ deliveryMethodsInWords }}</p>
                </div>

                <div v-if="showSettings" class="d-inline-block mt-2 mb-2" :style="{ width: '100%' }">

                    <!-- Delivery settings -->
                    <Row v-if="(localRecurringSettings.deliveryPlan || {}).automatic" class="mt-2"
                        :style="{ padding: '30px', boxShadow: 'inset 1px 1px 5px 1px #d6d6d6' }">

                        <!-- Schedule Frequency e.g) Daily, Weekly, Monthly, Yearly or Custom  -->
                        <Col span="24">
                        
                            <deliveryWidget 
                                :deliveryMethods="(((this.localRecurringSettings || {}).deliveryPlan || {}).methods  || [])"
                                :clientDetails="client"
                                :deliveryPhones="localRecurringSettings.deliveryPlan.sms.phones"
                                :deliveryMailAddress="localRecurringSettings.deliveryPlan.mail.email"
                                :deliveryMailSubject="localRecurringSettings.deliveryPlan.mail.subject"
                                :deliveryMailMessage="localRecurringSettings.deliveryPlan.mail.message"
                                :deliverySmsMessage="smsMessageCompiled"
                                :testSmsUrl="testSmsUrl"
                                :testEmailUrl="testEmailUrl"
                                :shortcodes="shortcodes"
                                @updatedDeliveryMethods="getDeliveryMethodsInWords()"
                                @updated:stage=""
                                @updated:deliveryPhones="localRecurringSettings.deliveryPlan.sms.phones = $event"
                                @updated:deliveryMailAddress="localRecurringSettings.deliveryPlan.mail.email = $event"
                                @updated:deliveryMailSubject="localRecurringSettings.deliveryPlan.mail.subject = $event"
                                @updated:deliveryMailMessage="localRecurringSettings.deliveryPlan.mail.message = $event">

                            </deliveryWidget>

                        </Col>

                    </Row>

                    <Alert v-else show-icon :style="{ padding: '18px 65px', boxShadow: '#cedee7 1px 1px 3px 1px inset' }">
                        Manual Sending
                        <Icon type="ios-bulb-outline" slot="icon"></Icon>
                        <template slot="desc">Only notify me but i will be the one to send this {{ resourceName }}</template>
                    </Alert>
                </div>

                <div v-if="showMessage" class="mt-3">
                    <Alert show-icon>
                        <Icon type="ios-bulb-outline" slot="icon"></Icon>
                        <template slot="desc">Make delivering {{ resourceNamePlural }} easy. Setup your {{ resourceNamePlural }} to be sent manually/automatically via email/sms and get notified on delivery.</template>
                    </Alert>
                </div>

            </template>

            <!-- Extra Content  -->
            <template v-if="showActionBtns" slot="extraContent">

                <Row>

                    <Col span="24 mt-2">
                        <Row>
                            <Col span="24">

                                <!-- Final Step Button -->
                                <Button v-if="isEditingStage" class="float-right" type="primary" size="large" @click="saveDeliveryPlan()">
                                    <span>{{ showDoneText ? 'Save Changes': 'Done' }}</span>
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
            recurringSettings: {
                type: Object,
                default: null
            },
            client: {
                type: Object,
                default: null
            },
            smsTemplate:{
                type: String,
                default: ''
            },
            smsTemplateData: {
                type: Object,
                default: null
            },
            testEmailUrl: {
                type: String,
                default: ''
            },
            resourceName:{
                type: String,
                default: '___'
            },
            resourceNamePlural:{
                type: String,
                default: '___'
            },
            disabled:{
                type: Boolean,
                default: false    
            },
            showHeader:{
                type: Boolean,
                default: false    
            },
            showCheckMark:{
                type: Boolean,
                default: false    
            },
            showToggleSwitch:{
                type: Boolean,
                default: false    
            },
            showSettings:{
                type: Boolean,
                default: false    
            },
            showMessage:{
                type: Boolean,
                default: false    
            },
            showActionBtns:{
                type: Boolean,
                default: false    
            },
            showDoneText:{
                type: Boolean,
                default: false    
            },
            isEditing: {
                type: Boolean,
                default: false 
            },
            url:{
                type: String,
                default: null
            }
        },
        data(){
            return {
                moment: moment,

                /*
                    We are using cloneDeep to create a coplete copy of the javascript object without
                    having reactivity to the main recurring settings. This is so that whatever changes 
                    we make to the localRecurringSettings, they must not affect the parent "Recurring Settings". 
                    We will only update the parent details when we save the changes to the database.
                */
                localRecurringSettings: _.cloneDeep( (this.recurringSettings || {}) ),
                isEditingStage: ((_.cloneDeep( (this.recurringSettings || {}) ).editing || {}).schedulePlan),

                isSavingRecurringDeliveryPlan: false,
                deliveryMethodsInWords: '',

                shortcodes: this.getShotCodes(),
                subjectShortCode: '',
                messageShortCode: '',
    
            } 
        },
        watch: {

            //  Watch for changes on the recurringSettings
            recurringSettings: {
                handler: function (val, oldVal) {
                    
                    //  Update the local recurringSettings value
                    this.localRecurringSettings = _.cloneDeep(val);

                    //  Update the editing schedule shortcut
                    this.isEditingStage = ((_.cloneDeep( (val || {}) ).editing|| {}).deliveryPlan);

                },
                deep: true
            }
        },
        computed: {
            getSelectedPhone(newPhone){
                this.localRecurringSettings.deliveryPlan.sms.phones.push(newPhone)
            },
            smsMessageCompiled: function(){
                var smsBuilder = require('./../../../components/_common/compiledText/smsCompiledText/'+this.smsTemplate+'.js');

                var smsMessage = smsBuilder.buildSms(this.smsTemplateData);

                //  Update the local message
                this.localRecurringSettings.deliveryPlan.sms.message = smsMessage;
            },
            emailMessageCompiled: function(){

                var message = this.localRecurringSettings.deliveryPlan.mail.message;

                var shortcodesymbols = Object.keys(this.shortcodes);
                var shortCodeValues = Object.values(this.shortcodes);

                var replaceThis, withThis;

                for(var x=0; x < _.size(this.shortcodes); x++){

                    replaceThis = shortcodesymbols[x]; 
                    withThis = shortCodeValues[x];    
                    message = message.split(replaceThis).join(withThis);

                }

                //  Update the local message
                this.localRecurringSettings.deliveryPlan.mail.message = message;

                //  Return the compiled message
                return message;
            }   
        },
        methods: {
            updateToggleChanges(newVal){
                this.localRecurringSettings.deliveryPlan.automatic = newVal;
                this.getDeliveryMethodsInWords();
            },
            getDeliveryMethodsInWords(){
                var devliveryMethods = (((this.localRecurringSettings || {}).deliveryPlan || {}).methods  || []);
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
            getShotCodes(){

                return [];
            },

            activateEditMode(){
                //  Get all the plans and their edit state
                var editingSchedulePlan = ( this.localRecurringSettings.editing.schedulePlan );
                var editingDeliveryPlan = ( this.localRecurringSettings.editing.deliveryPlan );
                var editingPaymentPlan = ( this.localRecurringSettings.editing.paymentPlan );
                console.log('************************************************************* ');
                console.log('editingDeliveryPlan1: ' + this.isEditingStage);

                //  If we are still editing the schedule/payment plan 
                if( editingSchedulePlan || editingPaymentPlan ){
                    //  Tell the user to save first before editing
                    this.$Notice.warning({
                        title: 'Save '+(editingSchedulePlan ? 'Schedule Plans': 'Payment Plans')+'!',
                        desc: 'Save your '+(editingSchedulePlan ? 'Schedule Plans': 'Payment Plans')+' first before editing your Schedule Plans',
                    });
                }else{
                    this.localRecurringSettings.editing.deliveryPlan = true;
                    this.isEditingStage = true;
                }

                console.log('************************************************************* ');
                console.log('editingDeliveryPlan2: ' + this.isEditingStage);

            },
            saveDeliveryPlan(){

                var self = this;

                //  Start loader
                self.isSavingRecurringDeliveryPlan = true;

                console.log('Attempt to save recurring delivery plan...');;

                //  Form data to send
                let RecurringSettingsData = { settings: self.localRecurringSettings };

                if( this.url ){

                    //  Use the api call() function located in resources/js/api.js
                    api.call('post', this.url, RecurringSettingsData)
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

            }
        },
        created(){
            this.getDeliveryMethodsInWords();
        }
    }
</script>
