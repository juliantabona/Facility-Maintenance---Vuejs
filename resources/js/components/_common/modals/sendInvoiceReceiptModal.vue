<template>
    <div>
        <!-- Modal -->
        <mainModal 
            v-bind="$props" 
            :width="650"
            :isSaving="isSending" 
            :hideModal="hideModal"
            title="Send Receipt"
            :okText="(stage == 1) ? '' : okText" 
            :cancelText="(stage == 1) ? '' : 'Cancel'"
            @on-ok="send()" 
            @visibility="$emit('visibility', $event)">

            <template slot="content">

                <deliveryWidget 
                    :deliveryMethods="deliveryMethods"
                    :clientDetails="localInvoice.customized_client_details"
                    :deliveryPhones="localDeliveryPhones"
                    :deliveryMailAddress="localDeliveryMailAddress"
                    :deliveryMailSubject="localDeliveryMailSubject"
                    :deliveryMailMessage="localDeliveryMailMessage"
                    :deliverySmsMessage="smsMessageCompiled"
                    :testSmsUrl="'/api/invoices/'+localInvoice.id+'/receipts/send?test=1'"
                    :testEmailUrl="'/api/invoices/'+localInvoice.id+'/receipts/send?test=1'"
                    :shortcodes="shortcodes"
                    :showSmsPhoneImg="false"
                    @updated:stage="stage = $event"
                    @updated:deliveryMethods="deliveryMethods = $event"
                    @updated:deliveryPhones="localDeliveryPhones = $event"
                    @updated:deliveryMailAddress="localDeliveryMailAddress = $event"
                    @updated:deliveryMailSubject="localDeliveryMailSubject = $event"
                    @updated:deliveryMailMessage="localDeliveryMailMessage = $event">
                </deliveryWidget>

            </template>
            
        </mainModal>    
    </div>
</template>
<script>

    import moment from 'moment';
    import mainModal from './main.vue';
    import froalaEditor from './../wiziwigEditors/froalaEditor.vue'; 
    import Loader from './../loaders/Loader.vue';
    import deliveryWidget from './../../../widgets/delivery/show/main.vue';

    export default {
        props:{
            invoice: {
                type: Object,
                default: null
            },
            subject: {
                type: String,
                default: ''
            },
            email: {
                type: String,
                default: ''
            },
            message: {
                type: String,
                default: ''
            }
        },
        components: { mainModal, froalaEditor, Loader, deliveryWidget },
        data(){
            return{
                moment: moment,

                localInvoice: _.cloneDeep(this.invoice),
                deliveryMethods: ['Sms' /*, Email */],
                localDeliveryPhones: [],
                localDeliveryMailSubject: this.subject || 'Receipt For Invoice #'+this.invoice['reference_no_value'],
                localDeliveryMailAddress: this.invoice.customized_client_details.email,
                localDeliveryMailMessage: this.emailMsg(),
                shortcodes: this.getShotCodes(),
                
                hideModal: false,
                isLoading: false,
                isSending: false,
                stage: 1
            }
        },
        computed: {
            smsMessageCompiled: function(){

                var referenceNo = this.invoice.reference_no_value;
                var currency = (((this.invoice || {}).currency_type || {}).currency || {}).symbol || '';
                var grand_total = this.formatPrice( (this.invoice.grand_total || 0), currency);
                var company = ((this.invoice || {}).customized_company_details || {});

                var characterLimit = 160;
                //  Company info text limit = 23
                var companyName = this.truncate(company.name.trim(), 21) + ( company.name.length <= 21 ? ':' : '' );       //  Optimum Quality: 
                //  Reference text limit = 16
                var reference = 'Invoice #'+referenceNo;                        //  Invoice #002
                //  Amount text limit = 20
                var amount = 'Amount ' + grand_total;                           //  Amount P350.00
                
                var charLeft = (characterLimit - (companyName+reference+amount).length);
                var message = this.truncate(companyName+' Payment confirmation for '+reference+', '+amount+'. Thank you :)' , charLeft);    //  Optimum Quality: Payment confirmation for Invoice #002, Amount P350.00 :)'

                //  Return the compiled message
                return message;
            },
            okText: function(){
                var smsText = this.deliveryMethods.includes('Sms') ? 'Sms': '';
                var emailText = this.deliveryMethods.includes('Email') ? 'Email': '';
                var andText = this.deliveryMethods.includes('Sms') && this.deliveryMethods.includes('Email') ? ' & ': '';
                
                return 'Send '+( smsText )+( andText )+( emailText );
            }
        },
        methods: {
            emailMsg(){
                
                var money = this.invoice.grand_total || 0;
                var currency = (((this.invoice || {}).currency_type || {}).currency || {}).symbol || '';
                var amount = this.formatPrice(money, currency);
                var company_name = this.invoice.customized_company_details.name;

                return '<p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:12px;font-family:arial, \'helvetica neue\', helvetica, sans-serif;line-height:18px;color:#000000;">  \
                            Good day,  \
                        </p> \
                        <br> \
                        <p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:12px;font-family:arial, \'helvetica neue\', helvetica, sans-serif;line-height:18px;color:#000000;">  \
                            Please find attached receipt for <strong>Invoice #'+this.invoice['reference_no_value']+'</strong> \
                            created on your account as confirmation of payment for the balance of  \
                            <strong>'+amount+'. \
                        </p> \
                        <br> \
                        <p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:12px;font-family:arial, \'helvetica neue\', helvetica, sans-serif;line-height:18px;color:#000000;">  \
                            Thank you for your business. \
                        </p> \
                        <br> \
                        <p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:12px;font-family:arial, \'helvetica neue\', helvetica, sans-serif;line-height:18px;color:#000000;">  \
                            Regards, \
                            <br> \
                            '+company_name+' \
                        </p>';
            },
            formatPrice(money, symbol) {
                let val = (money/1).toFixed(2).replace(',', '.');
                return symbol + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            },
            truncate(string, limit){
                return (string.length > limit) ? string.substring(0, limit - 3)+'...' : string;
            },
            getShotCodes(){

                var money = this.invoice.grand_total || 0;
                var currency = (((this.invoice || {}).currency_type || {}).currency || {}).symbol || '';
                var sub_total = this.formatPrice( (this.invoice.sub_total_value || 0), currency);
                var grand_total = this.formatPrice( (this.invoice.grand_total || 0), currency);
                var client = ((this.invoice || {}).customized_client_details || {});
                var company = ((this.invoice || {}).customized_company_details || {});

                var shortcodes = {
                    '[invoice_heading]': this.invoice.heading,
                    '[invoice_reference_no]': '#'+this.invoice.reference_no_value,
                    '[created_date]': moment(this.invoice.created_date).format('MMM DD YYYY'),
                    '[expiry_date]': moment(this.invoice.expiry_date).format('MMM DD YYYY'),
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
            send(){

                var self = this;

                //  Start loader
                self.isSending = true;

                var data = {
                        sms: {
                            phones: this.localDeliveryPhones,
                            message: this.smsMessageCompiled,
                        },
                        mail: {
                            primaryEmails: [
                                this.localDeliveryMailAddress
                            ],
                            ccEmails: [],
                            bccEmails: [],
                            subject: this.localDeliveryMailSubject,
                            message: this.localDeliveryMailMessage
                        },
                        deliveryMethods: this.deliveryMethods
                    }

                console.log('Attempt to send receipt data...');
                console.log(data);

                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/invoices/'+self.localInvoice.id+'/receipts/send', data)
                    .then(({ data }) => {

                        console.log(data);

                        //  Stop loader
                        self.isSending = false;
                        
                        //  Alert parent and pass updated invoice data
                        //  NOTE that "data = updated invoice"
                        self.$emit('sent', data);

                        //  Alert creation success
                        self.$Message.success('Receipt sent sucessfully!');

                        //  Close the modal
                        self.closeModal();

                    })         
                    .catch(response => { 
                        //  Stop loader
                        self.isSending = false;

                        console.log('sendInvoiceReceiptModal.vue - Error sending receipt...');
                        console.log(response);
                    });
            },
            closeModal(){
                this.hideModal = true;
            }
        }
    }
</script>