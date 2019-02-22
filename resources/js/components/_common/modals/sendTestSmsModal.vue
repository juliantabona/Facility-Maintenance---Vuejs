<template>
    <div>
        <!-- Modal -->
        <mainModal 
            v-bind="$props" 
            :isSaving="isSending" 
            :hideModal="hideModal"
            title="Test Sending Invoice (Via SMS)"
            okText="Send" cancelText="Cancel"
            @on-ok="sendSms()" 
            @visibility="$emit('visibility', $event)">

            <template slot="content">

                <!-- Email Subject -->
                <Row :gutter="20">
                    <Col :span="24">
                        <Alert show-icon>
                            <Icon type="ios-bulb-outline" slot="icon"></Icon>
                            <template slot="desc">You can send a test sms to any number of you want. This is so that you can see how your sms will look like for your customers ;)</template>
                        </Alert>
                    </Col>
                    
                    <Col :span="24">
                        <p class="mb-1"><b>Phone Number:</b></p>
                        <phoneInput class="mb-2"  
                                    :deletable="false"
                                    :hidedable="false"
                                    :editable="true"
                                    :numberLimit="1"
                                    selectedType="mobile"
                                    :disabledTypes="['Telephone', 'Fax']"
                                    @updated="testPhoneNumber = $event">
                        </phoneInput>
                    </Col>
                </Row>
                
                <!-- Email Message -->
                <Row :gutter="20">
                    <Col :span="24">
                        <p :style="{ padding: '20px', boxShadow: 'inset 1px 1px 5px 1px #d6d6d6' }">
                            {{ locaMessage }}
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
    import phoneInput from './../../../components/_common/inputs/phoneInput.vue'; 

    export default {
        props:{
            url: {
                type: String,
                default: null
            },
            message: {
                type: String,
                default: ''
            }
        },
        components: { mainModal, Loader, phoneInput },
        data(){
            return{
                locaMessage: this.message,
                testPhoneNumber: '',
                hideModal: false,
                isSending: false
            }
        },
        methods: {
            sendSms(){
                
                if(this.url){

                    var self = this;

                    //  Start loader
                    self.isSending = true;

                    console.log('Attempt to send recurring invoice test sms...');

                    var smsData = {
                            phoneNumber: this.testPhoneNumber[0],
                            message: this.locaMessage,
                        }

                    console.log('smsData');
                    console.log(smsData);

                    //  Use the api call() function located in resources/js/api.js
                    api.call('post', self.url, smsData)
                    .then(({ data }) => {

                        console.log(data);

                        //  Stop loader
                        self.isSending = false;
                        
                        //  Alert parent and pass updated invoice data
                        //  NOTE that "data = updated invoice"
                        self.$emit('sent', data);

                        //  Alert creation success
                        self.$Message.success('Sms sent sucessfully!');

                        //  Close the modal
                        self.closeModal();

                    })         
                    .catch(response => { 
                        //  Stop loader
                        self.isSending = false;

                        console.log('sendTestSmsModal.vue - Error sending recurring invoice test sms...');
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