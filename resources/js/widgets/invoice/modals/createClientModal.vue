<template>
    <div>
        <!-- Modal -->
        <Modal title="Send Invoice" v-model="modalVisible" :mask-closable="true" @on-visible-change="detectClose">

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

            <!-- Send/Cancel Buttons -->
            <span slot="footer" class="dialog-footer">
                <el-button size="small" @click="closeModal">Cancel</el-button>
                <el-button size="small" type="primary" @click="saveChanges" :loading="isSending">Send Email</el-button>
            </span>
        </Modal>    
    </div>
</template>
<script>

    import Loader from './../../../components/_common/loader/Loader.vue';
    import froalaEditor from './../wiziwigEditors/froalaEditor.vue';   

    export default {
        props:{
            show: {
                type: Boolean,
                default: false,
            },
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
        components: { Loader, froalaEditor },
        data(){
            return{
                isSending: false,
                localInvoice: this.invoice,
                localSubject: this.subject || 'Invoice #'+this.invoice['reference_no_value'],
                localEmail: this.invoice.customized_client_details.email,
                localMessage: this.message || 'Good Day, <br/><br/> Please find attached invoice '+this.invoice['reference_no_value']+'. <br/><br/> Thank you.',
            
                modalVisible: false
            }
        },
        methods: {
            saveChanges(){

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
            detectClose(){
                
                var self = this;

                //  Only after 1 second
                setTimeout(function(){
                    //  Notify the parent on change of modal visibility
                    self.$emit('visibility', self.modalVisible)
                }, 500);
            },
            closeModal(){
                //  By setting modalVisible = false, we also trigger the detectClose() method
                //  since the modal has the event @on-visible-change="detectClose" to detect
                //  any changes of the "modalVisible". The "detectClose()" method would then
                //  notify the parent on the changes of the modal visibility.
                this.modalVisible = false;
            }
        },
        mounted: function () {
            //  Only after everything has loaded
            this.$nextTick(function () {
                //  Set modal visibility to true to show modal
                this.modalVisible = true;
            })
        }
    }
</script>