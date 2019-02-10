<template>
    <div>
        <!-- Modal -->
        <mainModal 
            v-bind="$props" 
            :isSaving="isSending" 
            :hideModal="hideModal"
            title="Send Invoice (Via Email)"
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
                localSubject: this.subject || 'Invoice #'+this.invoice['reference_no_value'],
                localEmail: this.invoice.customized_client_details.email,
                localMessage: this.message || 'Good Day, <br/><br/> Please find attached invoice '+this.invoice['reference_no_value']+'. <br/><br/> Thank you.',
            
                hideModal: false,
                isLoading: false,
                isSending: false
            }
        },
        methods: {
            sendEmail(){

                var self = this;

                //  Start loader
                self.isSending = true;

                console.log('Attempt to send invoice...');

                var emailData = {
                        email: this.localEmail,
                        subject: this.localSubject,
                        message: this.localMessage,
                    }

                //  Use the api call() function located in resources/js/api.js
                api.call('post', '/api/invoices/'+self.localInvoice.id+'/send', emailData)
                    .then(({ data }) => {

                        console.log(data);

                        //  Stop loader
                        self.isSending = false;
                        
                        //  Alert parent and pass updated invoice data
                        //  NOTE that "data = updated invoice"
                        self.$emit('sent', data);

                        //  Alert creation success
                        self.$Message.success('Invoice sent sucessfully!');

                        //  Close the modal
                        self.closeModal();

                    })         
                    .catch(response => { 
                        //  Stop loader
                        self.isSending = false;

                        console.log('sendInvoiceModal.vue - Error sending invoice...');
                        console.log(response);
                    });
            },
            closeModal(){
                this.hideModal = true;
            }
        }
    }
</script>