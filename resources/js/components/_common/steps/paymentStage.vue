<template>

    <div>

        <!-- Fade loader - Shows when recording invoice payment  -->
        <fadeLoader :loading="isRecordingPayment" msg="Recording payment, please wait..."></fadeLoader>

        <!-- Fade loader - Shows when cancelling invoice payment  -->
        <fadeLoader :loading="isCancelingPayment" msg="Cancelling payment, please wait..."></fadeLoader>

        <!-- Fade loader - Shows when updating invoice reminders  -->
        <fadeLoader :loading="isUpdatingReminders" msg="Updating reminders, please wait..."></fadeLoader>

        <!-- Stage card  -->
        <stagingCard 
            :stageNumber="3" :showHeader="false" 
            :disabled="isRecordingPayment || isCancelingPayment || isUpdatingReminders || (!localInvoice.has_sent && !localInvoice.has_skipped_sending)" :showVerticalLine="true"
            :leftWidth="10" :rightWidth="14">

            <!-- Left Content  -->
            <template slot="leftContent">

                <h4 v-if="" class="text-secondary">{{ localInvoice.has_paid ? 'Got Paid' : 'Get Paid' }}</h4>
                <p class="mt-2 mb-2"><span class="font-weight-bold">{{ (localInvoice.has_paid ? 'Amount Paid' : 'Amount Due') }}:</span> {{ localInvoice.grand_total_value | currency(currencySymbol)  }}</p>

            </template>

            <!-- Right Content  -->
            <template slot="rightContent">

                <!-- Animated checkmark  -->
                <animatedCheckmark v-if="localInvoice.has_paid"></animatedCheckmark>

                <!-- Focus Ripple  -->
                <focusRipple v-if="!localInvoice.has_paid" color="blue" :ripple="!localInvoice.has_paid" class="float-right">

                    <!-- Record Payment Button  -->
                    <Poptip confirm title="Are you sure this invoice have been paid?"  width="300"
                            ok-text="Yes" cancel-text="No" @on-ok="recordPayment()" placement="top">
                        
                        <Button v-if="localInvoice.has_sent || localInvoice.has_skipped_sending" type="primary" size="large">
                            <span>Record Payment</span>
                        </Button>

                    </Poptip>

                </focusRipple>

                <!-- Send Receipt Button  -->
                <focusRipple v-if="localInvoice.has_paid" color="blue" :ripple="!localInvoice.has_sent_receipt" class="float-right mt-2 ml-1 mr-2">

                    <!-- Send/Resend Button -->
                    <Button :type="localInvoice.has_sent_receipt ? 'default' : 'primary'" 
                            size="large" @click="isOpenSendInvoiceReceiptModal = true">
                        <span>{{ localInvoice.has_sent_receipt ? 'Resend Receipt' : 'Send Receipt'  }}</span>
                    </Button>

                </focusRipple>

                <!-- Cancel Payment Button  -->
                <Button v-if="localInvoice.has_paid" class="float-right mt-2" type="default" size="large" @click="cancelPayment()">
                    <span>Cancel Payment</span>
                </Button>
                
            </template>

            <!-- Extra Content  -->
            <template slot="extraContent">
                
                <Row :gutter="20" v-if="(localInvoice.has_sent || localInvoice.has_skipped_sending) && !localInvoice.has_paid">

                    <!-- Explainer Message  -->
                    <Col span="24">
                        <Alert :style="{ zIndex:'1' }">
                            <h6 class="mt-2 mb-2"><span class="font-weight-bold">Status:</span> Your invoice is awaiting payment for - {{ localInvoice.grand_total_value | currency(currencySymbol) }}</h6>
                        </Alert>
                    </Col>

                    <!-- Schedule Client Reminders -->
                    <Row>
                        <Col span="24">
                            <div style="background:#eee;padding: 20px">
                                <Card :bordered="false">
                                    <Row>
                                        <Col span="24" class="mb-3">
                                            <h6>
                                                <Icon type="ios-information-circle-outline" :size="24" :style="{ marginTop:'-3px' }" />
                                                <span class="font-weight-bold">Get paid on time by scheduling payment reminders for your customer:</span>
                                                <Alert class="mt-2 mb-1" :style="{ zIndex:'1' }">
                                                    <span class="font-weight-bold">NOTE:</span> Email reminders will be sent to - "{{ localInvoice.customized_client_details.email }}"
                                                </Alert>
                                            </h6>
                                        </Col>

                                        <!-- Reminders - Before Due Date  -->
                                        <Col span="8">
                                            <h6 class="text-secondary mb-3">1) Remind before due date</h6>
                                            <CheckboxGroup v-model="paymentReminderTime">
                                                <Checkbox class="font-weight-bold" label="1-b">1 day before</Checkbox>
                                                <Checkbox class="font-weight-bold" label="3-b">3 days before</Checkbox>
                                                <Checkbox class="font-weight-bold" label="7-b">7 days before</Checkbox>
                                                <Checkbox class="font-weight-bold" label="14-b">14 days before</Checkbox>
                                            </CheckboxGroup>
                                        </Col>

                                        <!-- Reminders - On Due Date  -->
                                        <Col span="8" class="border-left border-right pl-3">
                                            <h6 class="text-secondary mb-3">2) Remind on due date</h6>
                                            <CheckboxGroup v-model="paymentReminderTime">
                                                <Checkbox class="font-weight-bold" label="0">On due date</Checkbox>
                                            </CheckboxGroup>
                                        </Col>

                                        <!-- Reminders - After Due Date  -->
                                        <Col span="8" class="pl-3">
                                            <h6 class="text-secondary mb-3">3) Remind after due date</h6>
                                            <CheckboxGroup v-model="paymentReminderTime">
                                                <Checkbox class="font-weight-bold" label="1-a">1 day after</Checkbox>
                                                <Checkbox class="font-weight-bold" label="3-a">3 days after</Checkbox>
                                                <Checkbox class="font-weight-bold" label="7-a">7 days after</Checkbox>
                                                <Checkbox class="font-weight-bold" label="14-a">14 days after</Checkbox>
                                            </CheckboxGroup>
                                        </Col>
                                        
                                        <Col span="24 mt-2 pt-3 border-top">
                                            <span class="font-weight-bold mr-1">Reminder Method: </span>

                                            <Row>
                                                <Col span="12">
                                                    <!-- Reminder method Selector -->
                                                    <Select v-model="paymentReminderMethod" :style="{ width:'250px' }" placeholder="Select reminder method" multiple filterable>
                                                        <Option value="email" key="email">Email</Option>
                                                        <Option value="sms" key="sms" :disabled="true">SMS</Option>
                                                    </Select>
                                                </Col>
                                            
                                                <Col span="12">
                                                    <!-- Save Button -->
                                                    <Button class="float-right" type="primary" size="large" @click="updatePaymentReminders()">
                                                        <span>Save Reminders</span>
                                                    </Button>
                                                </Col>
                                            </Row>

                                        </Col>
                                    </Row>
                                </Card>
                            </div>
                        </Col>
                    </Row>

                </Row>
                
            </template>
            
        </stagingCard>

        <!-- 
            MODAL TO SEND INVOICE RECEIPT - VIA EMAIL
        -->
        <sendInvoiceReceiptModal 
            v-if="isOpenSendInvoiceReceiptModal" 
            :invoice="localInvoice" 
            @visibility="isOpenSendInvoiceReceiptModal = $event"
            @sent="updateParent($event)">
        </sendInvoiceReceiptModal>

    </div>

</template>
<script type="text/javascript">

    import stagingCard from './main.vue';
    import fadeLoader from './../loaders/fadeLoader.vue';
    import animatedCheckmark from './../animatedIcons/animatedCheckmark.vue';
    
    /*  Ripples  */
    import focusRipple from './../wavyRipples/focusRipple.vue';

    /*  Modals  */
    import sendInvoiceReceiptModal from './../modals/sendInvoiceReceiptModal.vue';

    export default {
        components: { fadeLoader, stagingCard, animatedCheckmark, focusRipple, sendInvoiceReceiptModal },
        props: {
            invoice: {
                type: Object,
                default: null
            },
            editMode: {
                type: Boolean,
                default: false
            }
        },
        data(){
            return {
                isRecordingPayment: false,
                isCancelingPayment: false,
                isUpdatingReminders: false,
                isOpenSendInvoiceReceiptModal: false,
                localInvoice: this.invoice,

                currencySymbol: ((this.invoice.currency_type || {}).currency || {}).symbol,

                paymentReminderTime: this.getPaymentReminderTime(),
                paymentReminderMethod: this.getPaymentReminderMethod(),
            }
        },
        watch: {

            //  Watch for changes on the invoice
            invoice: {
                handler: function (val, oldVal) {

                    //  Update the local invoice value
                    this.localInvoice = val;

                    //  Update the currency value
                    this.currencySymbol = ((val.currency_type || {}).currency || {}).symbol;

                    //  Update the payment reminder times
                    this.paymentReminderTime = this.getPaymentReminderTime();

                    //  Update the payment reminder methods
                    this.paymentReminderMethod = this.getPaymentReminderMethod();

                },
                deep: true
            }
        },
        methods: {

            getPaymentReminderTime: function(){
                if( ((this.localInvoice || {}).reminders || {}).length ){
                    return this.localInvoice.reminders.map(reminder => reminder.days_after);
                }else{
                    return [];
                }

            },
            getPaymentReminderMethod: function(){
                if( ((this.localInvoice || {}).reminders || {}).length ){
                    return this.localInvoice.reminders.map(reminder => {
                        var can = [];

                        //  If this reminder can be emailed return 'email' value
                        if(reminder.can_email){
                            can.push('email');

                        //  If this reminder can be sms'd return 'sms' value
                        }else if(reminder.can_sms){
                            can.push('sms');
                        }

                        return can;

                    //  if result is [['email', 'email'], ['sms', 'sms']] then flatten to ['email', 'email', 'sms', 'sms']
                    }).flat()

                    //  if result is ['email', 'email', 'sms', 'sms'] then filter to only unique values ['email', 'sms']
                    .filter( (value, index, self) => { 
                        return self.indexOf(value) === index;
                    });
                }else{
                    return ['email'];
                }

            },
            recordPayment(){

                var self = this;

                //  Start loader
                self.isRecordingPayment = true;

                console.log('Attempt to record invoice payment...');

                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/invoices/'+self.localInvoice.id+'/payment')
                    .then(({ data }) => {

                        console.log(data);

                        //  Stop loader
                        self.isRecordingPayment = false;
                        
                        //  Alert creation success
                        self.$Message.success('Payment recorded sucessfully!');

                        //  Alert parent and pass updated invoice data
                        self.$emit('paid', data);

                    })         
                    .catch(response => { 
                        //  Stop loader
                        self.isRecordingPayment = false;

                        console.log('invoiceSummaryWidget.vue - Error recording invoice payment...');
                        console.log(response);
                    });
            },
            cancelPayment(){

                var self = this;

                //  Start loader
                self.isCancelingPayment = true;

                console.log('Attempt to cancel invoice payment...');

                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/invoices/'+self.localInvoice.id+'/cancel-payment')
                    .then(({ data }) => {

                        console.log(data);

                        //  Stop loader
                        self.isCancelingPayment = false;
                        
                        //  Alert creation success
                        self.$Message.success('Payment canceled sucessfully!');

                        //  Alert parent and pass updated invoice data
                        self.$emit('cancelled', data);

                    })         
                    .catch(response => { 
                        //  Stop loader
                        self.isCancelingPayment = false;

                        console.log('invoiceSummaryWidget.vue - Error canceling invoice payment...');
                        console.log(response);
                    });
            },
            updatePaymentReminders(){

                var self = this;

                //  Start loader
                self.isUpdatingReminders = true;

                var remindersData = { 
                    reminders: {
                        days: this.paymentReminderTime,
                        method: this.paymentReminderMethod
                    } 
                };

                console.log('Attempt to update invoice payment reminders...');

                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/invoices/'+self.localInvoice.id+'/reminders', remindersData)
                    .then(({ data }) => {

                        console.log(data);

                        //  Stop loader
                        self.isUpdatingReminders = false;
                        
                        //  Alert creation success
                        self.$Message.success('Reminder added sucessfully!');

                        //  Alert parent and pass updated invoice data
                        self.$emit('reminderSet', data);

                    })         
                    .catch(response => { 
                        //  Stop loader
                        self.isUpdatingReminders = false;

                        console.log('invoiceSummaryWidget.vue - Error updating invoice payment reminders...');
                        console.log(response);
                    });
            },
            sendInvoiceReceipt(){

                var self = this;

                //  Start loader
                self.isSendingInvoice = true;

                console.log('Attempt to send invoice...');

                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/invoices/'+self.localInvoice.id+'/send')
                    .then(({ data }) => {

                        console.log(data);

                        //  Stop loader
                        self.isSendingInvoice = false;
                        
                        //  Alert creation success
                        self.$Message.success('Invoice sent sucessfully!');

                        //  NOTE that "data = updated invoice"
                        self.updateParent(updatedInvoice);

                    })         
                    .catch(response => { 
                        //  Stop loader
                        self.isSendingInvoice = false;

                        console.log('invoiceSummaryWidget.vue - Error sending invoice...');
                        console.log(response);
                    });
            },
            updateParent(updatedInvoice){
                //  Alert parent and pass updated invoice data
                this.$emit('sent', updatedInvoice);
            }
        }
    }
</script>
