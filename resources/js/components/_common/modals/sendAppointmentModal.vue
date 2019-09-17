Inv<template>
    <div>
        <!-- Modal -->
        <mainModal 
            v-bind="$props" 
            :width="650"
            :isSaving="isSending" 
            :hideModal="hideModal"
            title="Send Appointment"
            :okText="(stage == 1) ? '' : okText" 
            :cancelText="(stage == 1) ? '' : 'Cancel'"
            @on-ok="send()" 
            @visibility="$emit('visibility', $event)">

            <template slot="content">

                <deliveryWidget 
                    :deliveryMethods="deliveryMethods"
                    :clientDetails="localAppointment.client"
                    :deliveryPhones="localDeliveryPhones"
                    :deliveryMailAddress="localDeliveryMailAddress"
                    :deliveryMailSubject="localDeliveryMailSubject"
                    :deliveryMailMessage="localDeliveryMailMessage"
                    :deliverySmsMessage="smsMessageCompiled"
                    :testSmsUrl="'/api/appointments/'+localAppointment.id+'/send?test=1'"
                    :testEmailUrl="'/api/appointments/'+localAppointment.id+'/send?test=1'"
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
            appointment: {
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
            },
            smsTemplate:{
                type: String,
                default: ''
            },
            smsTemplateData: {
                type: Object,
                default: null
            },
        },
        components: { mainModal, froalaEditor, Loader, deliveryWidget },
        data(){
            return{
                moment: moment,
                
                localAppointment: _.cloneDeep(this.appointment),
                deliveryMethods: ['Sms' /*, Email */],
                localDeliveryPhones: [],
                localDeliveryMailSubject: this.subject || 'Appointment #'+this.appointment['reference_no_value'],
                localDeliveryMailAddress: this.appointment.client.email,
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
                console.log('sms builder location');
                console.log('./../../../components/_common/compiledText/smsCompiledText/'+this.smsTemplate+'.js')

                var smsBuilder = require('./../../../components/_common/compiledText/smsCompiledText/'+this.smsTemplate+'.js');

                console.log('Builder Variable');
                console.log(smsBuilder);

                var smsMessage = smsBuilder.default.buildSms(this.smsTemplateData);

                console.log('smsMessage');
                console.log(smsMessage);

                return smsMessage;
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
                /*
                var money = this.appointment.grand_total || 0;
                var currency = (((this.appointment || {}).currency_type || {}).currency || {}).symbol || '';
                var amount = this.formatPrice(money, currency);
                var expiry_date = moment(this.appointment.expiry_date).format('MMM DD YYYY');
                var company_name = this.appointment.customized_company_details.name;

                return '<p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:12px;font-family:arial, \'helvetica neue\', helvetica, sans-serif;line-height:18px;color:#000000;">  \
                            Good day,  \
                        </p> \
                        <br> \
                        <p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-size:12px;font-family:arial, \'helvetica neue\', helvetica, sans-serif;line-height:18px;color:#000000;">  \
                            Please find attached <strong>Appointment #'+this.appointment['reference_no_value']+'</strong> \
                            created on your account for services rendered. Payment regarding the&nbsp;balance of  \
                            <strong>'+amount+' </strong> \
                            must be settled by the  \
                            <strong>'+expiry_date+'</strong>  \
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
                            '+company_name+' \
                        </p>';
                */
                return '';
            },
            formatPrice(money, symbol) {
                let val = (money/1).toFixed(2).replace(',', '.');
                return symbol + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            },
            truncate(string, limit){
                return (string.length > limit) ? string.substring(0, limit - 3)+'...' : string;
            },
            getShotCodes(){
                /*
                var money = this.appointment.grand_total || 0;
                var currency = (((this.appointment || {}).currency_type || {}).currency || {}).symbol || '';
                var sub_total = this.formatPrice( (this.appointment.sub_total_value || 0), currency);
                var grand_total = this.formatPrice( (this.appointment.grand_total || 0), currency);
                var client = ((this.appointment || {}).client || {});
                var company = ((this.appointment || {}).customized_company_details || {});

                var shortcodes = {
                    '[appointment_heading]': this.appointment.heading,
                    '[appointment_reference_no]': '#'+this.appointment.reference_no_value,
                    '[created_date]': moment(this.appointment.created_date).format('MMM DD YYYY'),
                    '[expiry_date]': moment(this.appointment.expiry_date).format('MMM DD YYYY'),
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
                */
                return {};
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

                console.log('Attempt to send appointment data...');
                console.log(data);

                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/appointments/'+self.localAppointment.id+'/send', data)
                    .then(({ data }) => {

                        console.log(data);

                        //  Stop loader
                        self.isSending = false;
                        
                        //  Alert parent and pass updated appointment data
                        //  NOTE that "data = updated appointment"
                        self.$emit('sent', data);

                        //  Alert creation success
                        self.$Message.success('Appointment sent sucessfully!');

                        //  Close the modal
                        self.closeModal();

                    })         
                    .catch(response => { 
                        //  Stop loader
                        self.isSending = false;

                        console.log('sendAppointmentModal.vue - Error sending appointment...');
                        console.log(response);
                    });
            },
            closeModal(){
                this.hideModal = true;
            }
        }
    }
</script>