<template>
    <div>
        <Modal
            title="Send Invoice"
            v-model="modalVisible"
            :mask-closable="true"
            @on-close="abortChanges">

            <Row :gutter="20">
                <Col :span="4">
                    <span class="text-right d-block font-weight-bold">Subject:</span>
                </Col>
                <Col :span="20">
                    <el-input placeholder="Email Subject" v-model="localSubject" size="mini" class="mb-1"></el-input>
                </Col>
            </Row>

            <Row :gutter="20">
                <Col :span="4">
                    <span class="text-right d-block font-weight-bold">Email:</span>
                </Col>
                <Col :span="20">
                    <el-input placeholder="Recipient email e.g) example@gmail.com" v-model="localEmail" size="mini" class="mb-2"></el-input>
                </Col>
            </Row>
            
            <Row :gutter="20">
                <Col :span="24">
                    <span class="d-block font-weight-bold mt-2 mb-2">Message:</span>
                    <Summernote :content.sync="localMessage" ></Summernote>                    
                </Col>
            </Row>

            <span slot="footer" class="dialog-footer">
                <el-button size="small" @click="abortChanges">Cancel</el-button>
                <el-button size="small" type="primary" @click="saveChanges" :loading="isSending">Send Email</el-button>
            </span>
        </Modal>    
    </div>
</template>
<script>

    import Loader from './../../../components/_common/loader/Loader.vue';
    import Summernote from './../../quotation/Summernote.vue';

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
        components: { Loader, Summernote },
        data(){
            return{
                isSending: false,
                localInvoice: this.invoice,
                localSubject: this.subject || 'Invoice #'+this.invoice['reference_no_value'],
                localEmail: this.invoice.customized_client_details.email,
                localMessage: this.message || 'Good Day, <br/><br/> Please find attached invoice '+this.invoice['reference_no_value']+'. <br/><br/> Thank you.',
            }
        },
        computed:{
            modalVisible:{
                get(){
                    return this.show;
                },
                set(v){ 
                    this.$emit("closed");
                }
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
                        
                        //  Alert creation success
                        self.$Message.success('Invoice sent sucessfully!');

                        //  Alert parent and pass updated invoice data
                        self.$emit('paid', data);

                    })         
                    .catch(response => { 
                        //  Stop loader
                        self.isSending = false;

                        console.log('sendInvoiceModal.vue - Error sending invoice...');
                        console.log(response);
                    });
            },
            abortChanges(){
                this.$emit('closed');
            }
        }
    }
</script>