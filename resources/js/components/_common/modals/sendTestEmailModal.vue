<template>
    <div>
        <!-- Modal -->
        <mainModal 
            v-bind="$props" 
            :isSaving="isSending" 
            :hideModal="hideModal"
            title="Test Sending Invoice (Via Email)"
            okText="Send" cancelText="Cancel"
            @on-ok="sendEmail()" 
            @visibility="$emit('visibility', $event)">

            <template slot="content">

                <!-- Email Subject -->
                <Row :gutter="20">
                    <Col :span="24">
                        <Alert show-icon>
                            <Icon type="ios-bulb-outline" slot="icon"></Icon>
                            <template slot="desc">You can send a test email to any email address you want. This is so that you can see how your email will look like for your customers :)</template>
                        </Alert>
                    </Col>
                    
                    <Col :span="24">
                        <p class="mb-1"><b>Email Address:</b></p>

                        <emailInput class="mb-2"  
                                    :emailAddress="emailAddress"
                                    @updated:email="emailAddress = $event">
                        </emailInput>

                    </Col>
                </Row>
                
                <!-- Email Message -->
                <Row :gutter="20">

                    <Col :span="24">
                        <b class="d-block mt-1 mb-1">Subject:</b>
                        <p :style="{ padding: '10px 20px', boxShadow: 'inset 1px 1px 5px 1px #d6d6d6' }">
                            {{ locaSubject }}
                        </p>                   
                    </Col>

                    <Col :span="24">
                        <b class="d-block mt-3 mb-1">Mail Body:</b>
                        <p :style="{ padding: '20px', boxShadow: 'inset 1px 1px 5px 1px #d6d6d6', maxHeight: '150px', overflowY: 'auto' }"
                            v-html="locaMessage">
                        </p>                   
                    </Col>

                </Row>

            </template>
            
        </mainModal>    
    </div>
</template>
<script>

    /*  Main Modal   */
    import mainModal from './main.vue';

    /*  Loaders   */
    import Loader from './../loaders/Loader.vue';

    /*  Inputs   */
    import emailInput from './../../../components/_common/inputs/emailInput.vue'; 

    export default {
        props:{
            url: {
                type: String,
                default: null
            },
            mailAddress: {
                type: String,
                default: ''
            },
            subject: {
                type: String,
                default: ''
            },
            message: {
                type: String,
                default: ''
            }
        },
        components: { mainModal, Loader, emailInput },
        data(){
            return{
                user: auth.user,
                locaSubject: this.subject,
                locaMessage: this.message,
                emailAddress: this.mailAddress || auth.user.email,
                hideModal: false,
                isSending: false
            }
        },
        methods: {
            sendEmail(){
                
                if(this.url){

                    var self = this;

                    //  Start loader
                    self.isSending = true;

                    var data = {
                            mail: {
                                primaryEmails: [
                                    this.emailAddress
                                ],
                                ccEmails: [],
                                bccEmails: [],
                                subject: this.locaSubject,
                                message: this.locaMessage
                            },
                            deliveryMethods: ['Email']
                        }

                    console.log('Attempt to send test email data...');
                    console.log(data);

                    //  Use the api call() function located in resources/js/api.js
                    api.call('post', self.url, data)
                        .then(({ data }) => {

                            console.log(data);

                            //  Stop loader
                            self.isSending = false;
                            
                            //  Alert parent and pass updated invoice data
                            //  NOTE that "data = updated invoice"
                            self.$emit('sent', data);

                            //  Alert creation success
                            self.$Message.success('Email sent sucessfully!');

                            //  Close the modal
                            self.closeModal();

                        })         
                        .catch(response => { 
                            //  Stop loader
                            self.isSending = false;

                            console.log('sendTestEmailModal.vue - Error sending test email...');
                            console.log(response);
                        });
                }
            },
            closeModal(){
                this.hideModal = true;
            }
        }
    }
</script>