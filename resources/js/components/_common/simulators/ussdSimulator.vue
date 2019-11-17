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
            <div v-if="!showUssdContentModal" class="homescreen-content">
                
                <Row :gutter="12" class="pt-5">

                    <Col :span="24" class="pr-2 pl-2 pb-2">

                        <Card>

                            <span class="font-weight-bold d-block">Access My Store</span>

                        </Card>

                    </Col>

                    <Col :span="24" class="pr-2 pl-2 pb-2">

                        <Card>

                            <span class="font-weight-bold d-block">Customer Simulator</span>

                            <p class="mt-2">
                                <span class="d-block"><span class="font-weight-bold text-dark">1: </span>Dial <span class="font-weight-bold text-primary">*175*{{ ussdInterface.code }}#</span> to access your store</span>
                                <span class="d-block"><span class="font-weight-bold text-dark">2: </span>Select Option (1) to start shopping</span>
                            </p>

                            <!-- Launch Simulator button -->
                            <div class="clearfix mt-2">
                                <Button type="success" size="small" class="float-right" @click.native="launchCustomerSimulator()">Launch Simulator</Button>
                            </div>

                        </Card>

                    </Col>

                    <Col :span="24" class="pr-2 pl-2 pb-2">

                        <Card @click.native="showUssdContentModal = true">
                            
                            <span class="font-weight-bold d-block">Staff Simulator</span>
                            
                            <p class="mt-2">
                                <span class="d-block"><span class="font-weight-bold text-dark">1: </span>Dial <span class="font-weight-bold text-primary">*185*{{ ussdInterface.code }}#</span> to access your store</span>
                                <span class="d-block"><span class="font-weight-bold text-dark">2: </span>Login using Email/Mobile number and Password</span>
                            </p>

                            <!-- Launch Simulator button -->
                            <div class="clearfix mt-2">
                                <Button type="success" size="small" class="float-right" @click.native="launchStaffSimulator()">Launch Simulator</Button>
                            </div>

                        </Card>

                    </Col>

                </Row>

            </div>

            <!-- Ussd info goes here -->
            <div v-if="showUssdContentModal" class="ussd-content-container">

                <Card :bordered="false" class="ussd-content">
                    <template v-if="!isSendingUssdResponse">

                        <!-- Ussd response goes here -->
                        <div>
                            <p v-html="ussdResponse" style="white-space: pre-wrap;"></p>
                        </div>

                        <!-- Ussd reply button -->
                        <el-input type="text" v-model="ussd_reply" size="small" class="w-100 mt-2" placeholder="Reply..."></el-input>

                        <!-- Send/Cancel buttons -->
                        <div class="clearfix mt-2">
                            <Button type="error" size="large" class="float-right" @click="handleUssdReply()">Send</Button>
                            <Button type="default" size="large" @click="showUssdContentModal = false" class="float-right mr-2">Cancel</Button>
                        </div>

                    </template>

                    <!-- Loader -->
                    <Loader v-if="isSendingUssdResponse" :loading="true" type="text" class="text-left mt-2">{{ ussdLoaderText }}</Loader>

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
            postURL:{
                type: String,
                default: '/api/ussd/customer'
            },
            ussdInterface:{
                type: Object,
                default: null
            },
            ussdLoaderText:{
                type: String,
                default: 'USSD Code running'
            }
        },
        data(){
            return {
                ussd_text: '',
                ussd_reply: '',
                ussdResponse: '',
                phoneNumber: '+26700000000',
                showUssdContentModal: true,
                isSendingUssdResponse: false,

                showMobileStoreInfo: false,
                showStaffAccessInstructions: false,
                showCustomerAccessInstructions: false
            }
        },

        methods: {
            showUssdPopup(){
                this.showUssdContentModal = true;
            },
            hideUssdPopup(){
                this.showUssdContentModal = false;
            },
            launchCustomerSimulator(){
                this.resetUssdSimulator();
                this.ussd_reply = '1*'+this.ussdInterface.code;
                this.handleUssdReply();
                this.showUssdPopup();
            },
            launchStaffSimulator(){
                this.resetUssdSimulator();
                this.ussd_reply = '1*'+this.ussdInterface.code;
                this.handleUssdReply();
                this.showUssdPopup();
            },
            resetUssdSimulator(){
                this.ussd_reply = '';
                this.ussd_text = '';
            },
            handleUssdReply() {

                if( this.ussd_text == '' ){
                    this.ussd_text += this.ussd_reply;
                }else{
                    this.ussd_text += '*'+this.ussd_reply;
                }

                this.ussd_reply = '';
                this.callUssdEndpoint();
            },
            callUssdEndpoint() {  

                var self = this;

                //  Store data
                let ussdData = {
                    text: this.ussd_text,
                    phoneNumber: this.phoneNumber,
                    test_mode: true
                };

                self.isSendingUssdResponse = true;
                
                console.log( self.postURL );
                console.log( ussdData );

                //  Use the api call() function located in resources/js/api.js
                return api.call('post', self.postURL, ussdData)
                    .then(({data}) => {

                        console.log(data);

                        self.ussdResponse = data.substr(4);

                        /*  If the first 3 characters equal the text "END"  */
                        if( data.substr(3) == 'END' ){

                            /*  Reset the simulator  */ 
                            this.resetUssdSimulator();

                            /*  Hide the Ussd Popup  */
                            this.hideUssdPopup();

                        }

                        //  Reset the custom errors
                        self.isSendingUssdResponse = false;
                        
                    })         
                    .catch(response => { 
                    
                        console.log(response);

                        //  Stop loader
                        self.isSendingUssdResponse = false;     
        
                    });

            }

        },

        created(){
            this.callUssdEndpoint();
        }

    }

</script>