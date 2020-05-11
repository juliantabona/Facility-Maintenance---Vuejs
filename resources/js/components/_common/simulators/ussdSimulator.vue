<style scoped>
        
    /*  Import HTML5 mockups of popular devices
        - https://marvelapp.github.io/devices.css
        - https://github.com/pixelsign/html5-device-mockups
    */
    @import '/css/devices.min.css';

    .homescreen-content{
        position: absolute;
        padding: 0% 5% 0%;
        bottom: 0;
        right: 0;
        left: 0;
        top: 0;
    }

    .ussd-content-container{
        position: absolute;
        padding: 30% 5% 0%;
        bottom: 0;
        right: 0;
        left: 0;
        top: 0;
    }

    .ussd-content-container >>> .ussd-heading{
        top: 2px;
        z-index: 2;
        color: #fff;
        padding: 4px 10px;
        position: relative;
        border-radius: 5px 5px 0 0;
    }

    .ussd-content-container >>> .online{
        background: #19be6b;
    }

    .ussd-content-container >>> .offline{
        background: #ed4014;
    }

    .ussd-content-container >>> .ussd-content{
        z-index:2;
    }

    .ussd-content-container >>> .overlay{
        background: #00000050;
        position: absolute;
        z-index:1;
        bottom: 0;
        right: 0;
        left: 0;
        top: 0;
    }

    .el-form-item{
        margin-bottom: 0px;
    }

    .el-form-item >>> .el-form-item__label {
        margin: 0;
        padding: 0;
        line-height: 2em;
    }

    .el-form-item.phone-number >>> .el-form-item__content {
        line-height: inherit;
        display: inline-block;
    }

    .el-form-item.is-error{
        margin-bottom: 22px !important;
    }

</style>

<template>

    <div class="marvel-device nexus5">
        <div class="top-bar"></div>
        <div class="sleep"></div>
        <div class="volume"></div>
        <div class="camera"></div>
        <div class="screen">

            <!-- Homescreen info goes here -->
            <div v-show="!showUssdContentModal" class="homescreen-content">
                
                <Row :gutter="12" class="pt-5">

                    <Col :span="24" class="pr-2 pl-2 pb-2">

                        <Card>

                            <span class="font-weight-bold d-block">Access My {{ applicationName }}</span>

                        </Card>

                    </Col>

                    <Col :span="24" class="pr-2 pl-2 pb-2">

                        <Card>
                            
                            <span class="font-weight-bold d-block">Application Simulator</span>

                            <p class="mt-2">
                                <span class="d-block">
                                    Inform your customers to Dial 
                                    <span v-if="dedicated_code" class="font-weight-bold text-primary">{{ dedicated_code }}</span> 
                                    <span v-if="dedicated_code && shared_code"> or </span> 
                                    <span v-if="shared_code" class="font-weight-bold text-primary">{{ shared_code }}</span> 
                                     to visit your {{ applicationName }} on their mobile phones. Click <span class="font-weight-bold text-primary">Launch Simulator</span> 
                                     to see how your customers view your store.
                                </span>
                            </p>

                            <!-- Launch Simulator button -->
                            <div class="clearfix mt-2">

                                <Poptip trigger="hover" 
                                        class="float-right"
                                        placement="top-end"
                                        word-wrap width="250" 
                                        content="Launch Simulator to have a glimpse of what your customers see when visiting your store">
                                    <Button type="success" size="small" @click.native="launchUssdServiceSimulator()">Launch Simulator</Button>
                                </Poptip>
                            </div>

                        </Card>

                    </Col>

                </Row>

            </div>

            <!-- Ussd info goes here -->
            <div v-show="showUssdContentModal" class="ussd-content-container">
                    
                <Poptip trigger="hover" :content="liveModeStatusMsg" word-wrap width="300">
                        
                    <span :class="'ussd-heading' + (localUssdService.live_mode ? ' online' : ' offline')">
                        <span>Your {{ applicationName }} is {{ (localUssdService.live_mode ? 'Online' : 'Offline') }}</span>
                    </span>

                </Poptip>

                <Card :bordered="false" class="ussd-content">

                    <div v-show="!isSendingUssdResponse">

                        <!-- Ussd response goes here -->
                        <div>
                            <p v-html="ussdResponse" style="white-space: pre-wrap;"></p>
                        </div>

                        <!-- Ussd reply button -->
                        <el-input 
                            ref="reply_input"
                            type="text" v-model="ussd.msg" size="small" 
                            class="ussd_input w-100 mt-2" placeholder=""
                            @keyup.enter.native="callUssdEndpoint()"
                            @keyup.escape.native="closeUssdSimulator()">
                        </el-input>

                    </div>

                    <!-- Loader -->
                    <Loader v-show="isSendingUssdResponse" :loading="true" type="text" class="text-left mt-2">{{ ussdLoaderText }}</Loader>

                    <!-- Send/Cancel buttons -->
                    <div v-if="isSendingUssdResponse == false" class="ussd_reply_container mt-3 d-flex">
                        
                        <Poptip trigger="hover" content="Press ENTER on keyboard" 
                                class="float-right" placement="bottom-end" word-wrap width="220">
                            <span class="ussd_btn font-weight-bold ml-4 text-primary" @click="closeUssdSimulator()">Cancel</span>
                        </Poptip>
                        
                        <template v-if="ussd.requestType == 2">
                        
                            <span class="text-grey-light">|</span>

                            <Poptip trigger="hover" content="Press ESC on keyboard" class="float-right mr-2" 
                                    :placement="isSendingUssdResponse ? 'bottom-end' : 'bottom'" word-wrap width="200">
                                <span class="ussd_btn font-weight-bold mr-4 text-primary" @click="callUssdEndpoint()">Send</span>
                            </Poptip>

                        </template>

                    </div>

                </Card>

                <div class="overlay"></div>

            </div>

            <img src="/images/backgrounds/screensaver-01.jpg" style="width:100%;">

        </div>

    </div>

</template>

<script>

    /*  Loaders   */
    import Loader from './../loaders/Loader.vue'; 

    /*  Buttons  */
    import basicButton from './../buttons/basicButton.vue';

    export default {
        components: { Loader, basicButton },
        props: {
            ussdService:{
                type: Object,
                default: null
            },
            ussdLoaderText:{
                type: String,
                default: 'USSD Code running'
            },
            defaultUssdReply:{
                type: String,
                default: ''
            },
            applicationName:{
                type: String,
                default: 'Application'
            }
        },
        data(){
            return {
                ussd: {
                    serviceCode: null,
                    msg: this.defaultUssdReply,
                    requestType: 1,
                    sessionId: null,
                    msisdn: null
                },

                ussdResponse: '',
                localUssdService: null,
                showUssdContentModal: false,
                isSendingUssdResponse: false,
                //  phoneNumber: '+26700000000',
            }
        },
        watch: {
            /*  Keep track of changes on the ussdService.  */
            ussdService: {

                handler: function (val, oldVal) {

                    this.localUssdService = val;

                },
                deep: true

            }
        },
        computed: {
            liveModeStatusMsg(){
                if( this.localUssdService.live_mode == true ){
                    return 'This means that your '+this.applicationName+' is Online and can be accessed by your customers using their mobile phones.';
                }else{
                    return 'This means that your '+this.applicationName+' is Offline and can\'t be accessed by your customers. Turn on Live Mode to allow access for your customers.';
                }
            },
            shared_code(){
                return this.ussdService._embedded.service_code.shared_code;
            },
            dedicated_code(){
                return this.ussdService._embedded.service_code.dedicated_code;
            }
        },
        methods: {
            updateServiceCode(){

                //  If we have the shared code then use it as the service code
                if( this.shared_code ){
                    this.ussd.serviceCode = this.shared_code;
                }

                //  If we have the dedicated code then use it as the service code
                if( this.dedicated_code ){
                    this.ussd.serviceCode = this.dedicated_code;
                }

            },
            showUssdPopup(){
                this.showUssdContentModal = true;
                this.focusOnReplyInput();
            },
            hideUssdPopup(){
                this.showUssdContentModal = false;
            },
            launchUssdServiceSimulator(){
                this.resetUssdSimulator();
                this.callUssdEndpoint();
                this.showUssdPopup();
            },
            closeUssdSimulator(){
                this.resetUssdSimulator();
                this.hideUssdPopup();
            },
            resetUssdSimulator(){
                this.ussd.sessionId = null;
                this.ussd.requestType = 1;
                this.updateServiceCode();
                this.emptyInput();
            },
            redirectUssdSimulator( serviceCode ){

                //  Reset the Ussd Simulator
                this.resetUssdSimulator();

                //  Update the service code with the redirect service code
                this.ussd.serviceCode = serviceCode;

                //  Recall the Ussd end point
                this.callUssdEndpoint();
                
            },
            emptyInput(){
                this.ussd.msg = '';
            },
            focusOnReplyInput(){

                const self = this;

                this.$nextTick(() => {

                    //  Focus on the reply input field
                    self.$refs.reply_input.$refs.input.focus();

                });


            },
            callUssdEndpoint() {  

                var self = this;

                //  If this is the first request then embbed the service code within the message
                if( this.ussd.requestType == 1 ){

                    this.ussd.msg = this.ussd.serviceCode;

                }

                //  Store data
                let ussdData = {
                    
                    testMode: true,
                    msg: this.ussd.msg,
                    msisdn: this.ussd.msisdn,
                    sessionId: this.ussd.sessionId,
                    requestType: this.ussd.requestType,
                    
                };

                self.$emit('loading', true);

                //  Start loader
                self.isSendingUssdResponse = true;

                //  Use the api call() function located in resources/js/api.js
                return api.call('post', self.ussdService._links['oq:ussd_service_builder'].href, ussdData)
                    .then(({data}) => {

                        //  Stop loader
                        self.isSendingUssdResponse = false;

                        //  Update Ussd Response Message
                        self.ussdResponse = (data || {}).msg;
                        
                        //  Update Ussd Response Type
                        self.ussd.requestType = (data || {}).request_type;
                        
                        //  Update Ussd Session Id
                        self.ussd.sessionId = (data || {}).session_id;

                        //  Update Ussd Service Code
                        self.ussd.serviceCode = (data || {}).service_code;

                        self.emptyInput();

                        self.$emit('response', data);

                        self.$emit('loading', false);

                        //  If the requestType = 2 it means we want to continue the current session 
                        if( self.ussd.requestType == 2 ){

                            //  Focus on the reply input
                            self.focusOnReplyInput();


                        //  If the requestType = 5 it means we want to redirect 
                        }else if( self.ussd.requestType == 5 ){

                            //  Note: self.ussdResponse contains the new "Ussd Service Code" that we must redirect to
                            self.redirectUssdSimulator( self.ussdResponse );

                            //  Focus on the reply input
                            self.focusOnReplyInput();

                        }
                        
                    })         
                    .catch(response => { 
                    
                        console.log(response);

                        self.$emit('loading', false);

                        //  Stop loader
                        self.isSendingUssdResponse = false;     
        
                    });

            }

        },
        created() {
            //  Get and assign the ussd code
            this.updateServiceCode();
            this.localUssdService = this.ussdService;
        },

    }

</script>