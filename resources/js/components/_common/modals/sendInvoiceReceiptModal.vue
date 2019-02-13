<template>
    <div>
        <!-- Modal -->
        <mainModal 
            v-bind="$props" 
            :isSaving="isSending" 
            :hideModal="hideModal"
            title="Send Receipt (Via Email)"
            okText="Send" cancelText="Cancel"
            @on-ok="sendEmail()" 
            @visibility="$emit('visibility', $event)">

            <template slot="content">

                <!-- Email Subject -->
                <Row :gutter="20">
                    <Col :span="4">
                        <span class="text-right d-block font-weight-bold">Subject:</span>
                    </Col>
                    <Col :span="20">
                        <el-input placeholder="Email Subject" v-model="localSubject" size="mini" class="mb-1"></el-input>
                    </Col>
                </Row>

                <!-- Email Address -->
                <Row :gutter="20">
                    <Col :span="4">
                        <span class="text-right d-block font-weight-bold">Email:</span>
                    </Col>
                    <Col :span="20">
                        <el-input placeholder="Recipient email e.g) example@gmail.com" v-model="localEmail" size="mini" class="mb-2"></el-input>
                    </Col>
                </Row>
                
                <!-- Email Message -->
                <Row :gutter="20">
                    <Col :span="24">
                        <span class="d-block font-weight-bold mt-2 mb-2">Message:</span>
                        <froalaEditor :content.sync="localMessage" ></froalaEditor>                    
                    </Col>
                </Row>

            </template>
            
        </mainModal>    
    </div>
</template>
<script>

    import mainModal from './main.vue';
    import froalaEditor from './../wiziwigEditors/froalaEditor.vue'; 
    import Loader from './../loaders/Loader.vue';

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
        components: { mainModal, froalaEditor, Loader },
        data(){
            return{
                localInvoice: this.invoice,
                localSubject: this.subject || 'Receipt For Invoice #'+this.invoice['reference_no_value'],
                localEmail: this.invoice.customized_client_details.email,
                localMessage: this.emailMsg(),
                
                hideModal: false,
                isLoading: false,
                isSending: false
            }
        },
        methods: {
            emailMsg(){
                
                var money = this.invoice.grand_total_value || 0;
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
            sendEmail(){

                var self = this;

                //  Start loader
                self.isSending = true;

                console.log('Attempt to send invoice receipt...');

                var emailData = {
                        email: this.localEmail,
                        subject: this.localSubject,
                        message: this.localMessage,
                    }

                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/invoices/'+self.localInvoice.id+'/receipts/send', emailData)
                    .then(({ data }) => {

                        console.log(data);

                        //  Stop loader
                        self.isSending = false;
                        
                        //  Alert parent and pass updated invoice data
                        //  NOTE that "data = updated invoice"
                        self.$emit('sent', data);

                        //  Alert creation success
                        self.$Message.success('Invoice receipt sent sucessfully!');

                        //  Close the modal
                        self.closeModal();

                    })         
                    .catch(response => { 
                        //  Stop loader
                        self.isSending = false;

                        console.log('sendInvoiceReceiptModal.vue - Error sending invoice receipt...');
                        console.log(response);
                    });
            },
            closeModal(){
                this.hideModal = true;
            }
        }
    }
</script>