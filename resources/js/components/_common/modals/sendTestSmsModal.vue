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
                            <template slot="desc">You can send a test sms to any number you want. This is so that you can see how your sms will look like for your customers :)</template>
                        </Alert>
                    </Col>
                    
                    <Col :span="24">
                        <p class="mb-1"><b>Phone Number:</b></p>

                        <phoneInput class="mb-2"  
                                    :modelId="user.id" 
                                    :modelType="user.model_type" 
                                    :phones="testPhoneNumber" 
                                    :suggestedPhones="{ type: 'mobile', count: 1 }"
                                    :minLimit="1"
                                    :maxLimit="1"
                                    selectedType="mobile"
                                    :disabledTypes="['tel', 'fax']"                                                        
                                    :removable="true"
                                    :deletable="false"
                                    :hidedable="false"
                                    :editable="true"
                                    :removeDuplicates="true"
                                    :showIcon="false" 
                                    onIcon="" offIcon="" 
                                    title="" onText="" offText="" 
                                    poptipMsg=""
                                    @updated="testPhoneNumber = $event">
                        </phoneInput>

                    </Col>
                </Row>
                
                <!-- Email Message -->
                <Row :gutter="20">
                    <Col :span="24">
                        <b class="d-block mt-1 mb-1">Delivery Message:</b>
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
                user: auth.user,
                locaMessage: this.message,
                testPhoneNumber: [],
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

                    var data = {
                            sms: {
                                phones: [ this.testPhoneNumber[0] ],
                                message: this.locaMessage,
                            },
                            deliveryMethods: ['Sms']
                        }

                    console.log('Attempt to send test sms data...');
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
                        self.$Message.success('Sms sent sucessfully!');

                        //  Close the modal
                        self.closeModal();

                    })         
                    .catch(response => { 
                        //  Stop loader
                        self.isSending = false;

                        console.log('sendTestSmsModal.vue - Error sending test sms...');
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